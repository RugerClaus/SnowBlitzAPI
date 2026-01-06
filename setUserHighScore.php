<?php
include('db.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        $data = json_decode(file_get_contents("php://input"));
        $username = trim($data->username);
        $score = trim($data->score);

        if (!empty($username)) 
        {
            if (!empty($score))
            {
                try 
                {
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
                    $stmt->execute(["username" => $username]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if($user)
                    {
                        $user_id = $user["id"];
                        
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM leaderboard WHERE user_id = :user_id");
                        $stmt->execute(["user_id" => $user_id]);
                        $leaderboard_count = $stmt->fetchColumn();

                        if ($leaderboard_count > 0)
                        {
                            $stmt = $pdo->prepare("UPDATE leaderboard SET score = :score WHERE user_id = :user_id");
                            $stmt->execute(["score" => $score, "user_id" => $user_id]);

                            echo json_encode(["success" => true, "message" => "Score updated successfully"]);
                        }
                        else
                        {
                            $stmt = $pdo->prepare("INSERT INTO leaderboard (user_id,score) VALUES(:user_id, :score)");
                            $stmt->execute(["user_id" => $user_id, "score" => $score]);

                            echo json_encode(["success" => true, "message" => "New score added to leaderboard"]);
                        }
                    }
                    else
                    {
                        echo json_encode(["success" => false, "message" => "Username not found"]);
                    }
                }
                catch(PDOException $e)
                {
                    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
                }
            }
            else
            {
                echo json_encode(["success" => false, "message" => "Score cannot be empty"]);
            }
        }
        else 
        {
            echo json_encode(["success" => false, "message" => "Username cannot be empty"]);
        }
    } else 
    {
        echo json_encode(["success" => false, "message" => "Invalid request method"]);
    }
?>
