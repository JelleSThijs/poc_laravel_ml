from __future__ import annotations

import json
import math
import random
import sqlite3
import sys
from pathlib import Path
from typing import Any

ROOT = Path(__file__).resolve().parents[1]
DATABASE = ROOT / "storage" / "app" / "fruitmatch-ml.sqlite3"
LEARNING_RATE = 0.35
EXPLORATION_WEIGHT = 1.0


def connect() -> sqlite3.Connection:
    DATABASE.parent.mkdir(parents=True, exist_ok=True)
    connection = sqlite3.connect(DATABASE, timeout=5)
    connection.execute(
        """
        CREATE TABLE IF NOT EXISTS fruit_values (
            profile_id TEXT NOT NULL,
            fruit_id INTEGER NOT NULL,
            value REAL NOT NULL DEFAULT 0,
            plays INTEGER NOT NULL DEFAULT 0,
            PRIMARY KEY (profile_id, fruit_id)
        )
        """
    )
    return connection


def learn(request: dict[str, Any]) -> dict[str, str]:
    profile_id = request.get("profile_id")
    fruit_id = request.get("fruit_id")
    reward = request.get("reward")

    if not isinstance(profile_id, str) or not profile_id:
        raise ValueError("profile_id is required")
    if not isinstance(fruit_id, int) or fruit_id <= 0:
        raise ValueError("fruit_id must be a positive integer")
    if reward not in (-1, 1):
        raise ValueError("reward must be -1 or 1")

    with connect() as connection:
        row = connection.execute(
            "SELECT value, plays FROM fruit_values WHERE profile_id = ? AND fruit_id = ?",
            (profile_id, fruit_id),
        ).fetchone()
        current_value, plays = row if row else (0.0, 0)
        updated_value = current_value + LEARNING_RATE * (reward - current_value)
        connection.execute(
            """
            INSERT INTO fruit_values (profile_id, fruit_id, value, plays)
            VALUES (?, ?, ?, ?)
            ON CONFLICT(profile_id, fruit_id) DO UPDATE SET
                value = excluded.value,
                plays = excluded.plays
            """,
            (profile_id, fruit_id, updated_value, plays + 1),
        )

    return {"status": "learned"}


def recommend(request: dict[str, Any]) -> dict[str, int]:
    profile_id = request.get("profile_id")
    fruits = request.get("fruits")

    if not isinstance(profile_id, str) or not profile_id:
        raise ValueError("profile_id is required")
    if not isinstance(fruits, list) or not fruits:
        raise ValueError("fruits must be a non-empty list")

    candidates = [
        fruit["id"]
        for fruit in fruits
        if isinstance(fruit, dict)
        and isinstance(fruit.get("id"), int)
        and fruit["id"] > 0
    ]
    if not candidates:
        raise ValueError("fruits must include positive integer IDs")

    with connect() as connection:
        rows = connection.execute(
            "SELECT fruit_id, value, plays FROM fruit_values WHERE profile_id = ?",
            (profile_id,),
        ).fetchall()

    learned = {fruit_id: (value, plays) for fruit_id, value, plays in rows}
    total_plays = sum(plays for _, plays in learned.values())
    scored = []
    for fruit_id in candidates:
        value, plays = learned.get(fruit_id, (0.0, 0))
        exploration = EXPLORATION_WEIGHT * math.sqrt(
            math.log(total_plays + 2) / (plays + 1)
        )
        scored.append((value + exploration, fruit_id))

    best_score = max(score for score, _ in scored)
    best_ids = [
        fruit_id
        for score, fruit_id in scored
        if math.isclose(score, best_score, rel_tol=1e-9, abs_tol=1e-9)
    ]

    return {"fruit_id": random.choice(best_ids)}


def main() -> int:
    if len(sys.argv) != 2 or sys.argv[1] not in {"recommend", "feedback"}:
        print("Usage: recommender.py [recommend|feedback]", file=sys.stderr)
        return 2

    try:
        request = json.load(sys.stdin)
        if not isinstance(request, dict):
            raise ValueError("request must be a JSON object")
        response = learn(request) if sys.argv[1] == "feedback" else recommend(request)
        print(json.dumps(response))
        return 0
    except (json.JSONDecodeError, ValueError, sqlite3.Error) as error:
        print(str(error), file=sys.stderr)
        return 1


if __name__ == "__main__":
    raise SystemExit(main())