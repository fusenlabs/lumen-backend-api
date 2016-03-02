<?php namespace App\Http\Controllers;

use App\Score;

class ScoreController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function getUserPosition($genre = 'ROCK', $userId = 0, $friendsList = '0')
    {
        // calculate position
        $sentence = "SELECT s.`user_id`, MAX(s.`points`),
        (
            SELECT COUNT(*)+1 FROM (
                SELECT MAX(s3.`points`) as `points`, s3.`genre`, s3.`user_id` FROM `scores` as s3 WHERE s3.`genre` = ? AND s3.`user_id` IN ($friendsList) GROUP BY s3.`user_id` ORDER BY s3.`points` DESC
            ) as s2
            WHERE s2.`points` > (SELECT MAX(s4.`points`) FROM `scores` as s4 WHERE s4.user_id = s.user_id AND s4.`genre` = ?)
        ) as `position`
        FROM `scores` as s
        WHERE s.`genre` = ?
        GROUP BY s.`user_id`
        HAVING s.`user_id` = ?
        ORDER BY `position`";

        $result = app()->make('db')->select($sentence, array($genre, $genre, $genre, $userId));
        return $result[0]->position;
    }

    public function saveScore($userId, $genre, $points, $friendsList)
    {
        $score = Score::create([
            'user_id' => $userId,
            'genre' => $genre,
            'points' => $points
        ]);

        return $this->getUserPosition($genre, $userId, $friendsList);
    }

    public function getLeaderboard($genre = 'ROCK', $friendsList = '0', $top = 10)
    {
        // calculate position
        $sentence = "SELECT s.`user_id`, u.`name`, MAX(s.`points`) as `points`,
        (
            SELECT COUNT(*)+1 FROM (
                SELECT MAX(s3.`points`) as `points`, s3.`genre`, s3.`user_id` FROM `scores` as s3 WHERE s3.`genre` = ?  AND s3.`user_id` IN ($friendsList) GROUP BY s3.`user_id` ORDER BY s3.`points` DESC
            ) as s2
            WHERE s2.`points` > (SELECT MAX(s4.`points`) FROM `scores` as s4 WHERE s4.user_id = s.user_id AND s4.`genre` = ?)
        ) as `position`
        FROM `scores` as s
        LEFT JOIN `users` as u ON u.`id` = s.`user_id`
        WHERE s.`genre` = ? AND s.`user_id` IN ($friendsList)
        GROUP BY s.`user_id`
        ORDER BY `position`
        LIMIT 0,?";

        $result = app()->make('db')->select($sentence, array($genre, $genre, $genre, $top));
        return $result;
    }
}
