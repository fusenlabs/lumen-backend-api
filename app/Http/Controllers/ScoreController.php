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
        // It will use stored procedures when we're millionaire and can afford better servers
        $sentence = "SELECT s.`user_id`, MAX(s.`points`) as points, u.name,
        (
            SELECT COUNT(*)+1 FROM (
                SELECT MAX(s3.`points`) as `points`, s3.`genre`, s3.`user_id`, u3.`facebook_id` FROM `scores` as s3 LEFT JOIN users as u3 ON u3.id = s3.user_id WHERE s3.`genre` = ? AND u3.`facebook_id` IN ($friendsList) GROUP BY s3.`user_id` ORDER BY s3.`points` DESC
            ) as s2
            WHERE s2.`points` > (SELECT MAX(s4.`points`) FROM `scores` as s4 WHERE s4.user_id = s.user_id AND s4.`genre` = ?)
        ) as `position`
        FROM `scores` as s
        LEFT JOIN `users` as u ON u.`id` = s.`user_id`
        WHERE s.`genre` = ?
        GROUP BY s.`user_id`
        HAVING s.`user_id` = ?
        ORDER BY `position`";

        $result = app()->make('db')->select($sentence, array($genre, $genre, $genre, $userId));
        return (object)[
                "name" => $result[0]->name,
                "points" => $result[0]->points,
                "position" => $result[0]->position
            ];
        // $result[0]->position;
    }

    public function saveScore($userId, $genre, $points, $friendsList)
    {
        $score = Score::create([
            'user_id' => $userId,
            'genre' => $genre,
            'points' => $points
        ]);

        return $this->getUserPosition($genre, $userId, $friendsList)->position;
    }

    public function getLeaderboard($genre = 'ROCK', $friendsList = '0', $top = 10)
    {
        // calculate position
        // It will use stored procedures when we're millionaire and can afford better servers
        $sentence = "SELECT s.`user_id`, u.`name`, MAX(s.`points`) as `points`,
        (
            SELECT COUNT(*)+1 FROM (
                SELECT MAX(s3.`points`) as `points`, s3.`genre`, s3.`user_id`, u3.`facebook_id` FROM `scores` as s3 LEFT JOIN users as u3 ON u3.id = s3.user_id WHERE s3.`genre` = ?  AND u3.`facebook_id` IN ($friendsList) GROUP BY s3.`user_id` ORDER BY s3.`points` DESC
            ) as s2
            WHERE s2.`points` > (SELECT MAX(s4.`points`) FROM `scores` as s4 WHERE s4.user_id = s.user_id AND s4.`genre` = ?)
        ) as `position`
        FROM `scores` as s
        LEFT JOIN `users` as u ON u.`id` = s.`user_id`
        WHERE s.`genre` = ? AND u.`facebook_id` IN ($friendsList)
        GROUP BY s.`user_id`
        ORDER BY `position`
        LIMIT 0,?";

        $result = app()->make('db')->select($sentence, array($genre, $genre, $genre, $top));
        array_walk($result, function(&$item, $index) {
            $item = (object)[
                "name" => $item->name,
                "points" => $item->points,
                "position" => $item->position
            ];
        });
        return $result;
    }
}
