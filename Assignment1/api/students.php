<?php

//Required Headers
header("Access-Control_Allow_Origin: *");
header("Content-Type: application/json; charset = UTF-8");

//Include the database file
include_once 'connection.php';

//Create a student class
//(I'd normally put this in its own file but the instructions indicated to only create a student.php file)
 class Student
 {
     private $connection;
     private $table = "student";
     public $id;
     public $studName;
     public $studNum;
     public $studAge;


     public function __construct($db)
     {
         $this -> connection = $db;
     }


     public function update_student()
     {
         //Create SQL Query
         $query = "UPDATE " . $this -> table .
                      " SET student_name = :name,
                            student_age = :age,
                            student_number = :number
                      WHERE id = :id";

         //Prepare Query
         $statement = $this -> connection -> prepare($query);

         //Sanitize inputs
         $this -> studName = htmlspecialchars(strip_tags($this -> studName));
         $this -> studAge = htmlspecialchars(strip_tags($this -> studAge));
         $this -> studNum = htmlspecialchars(strip_tags($this -> studNum));
         $this -> studNum = htmlspecialchars(strip_tags($this -> studNum));
         $this -> id = htmlspecialchars(strip_tags($this -> id));

         //Bind values
         $statement -> bindParam(":name", $this -> studName);
         $statement -> bindParam(":age", $this -> studAge);
         $statement -> bindParam(":number", $this -> studNum);
         $statement -> bindParam(":id", $this -> id);

         //Execute Query
         if ($statement -> execute()) {
             return true;
         }

         return false;
     }


     public function delete_student()
     {
         //Create SQL Query
         $query = "DELETE FROM " . $this -> table .
                    " WHERE id = :id";

         //Prepare Query
         $statement = $this -> connection -> prepare($query);

         //Sanitize inputs
         $this -> studName = htmlspecialchars(strip_tags($this -> id));

         //Bind values
         $statement -> bindParam(":id", $this -> id);

         //Execute Query
         if ($statement -> execute()) {
             return true;
         }

         return false;
     }


     public function get_students()
     {
         //Create SQL Query
         $query = "SELECT * FROM " . $this -> table;
         //Prepare Query
         $statement = $this -> connection -> prepare($query);

         //Execute Query
         $statement -> execute();

         return $statement;
     }


     public function add_student()
     {
         //Create SQL Query
         $query = "INSERT INTO " . $this -> table .
                      " SET student_name = :name,
                           student_age = :age,
                           student_number = :number";

         //Prepare Query
         $statement = $this -> connection -> prepare($query);

         //Sanitize inputs
         $this -> studName = htmlspecialchars(strip_tags($this -> studName));
         $this -> studAge = htmlspecialchars(strip_tags($this -> studAge));
         $this -> studNum = htmlspecialchars(strip_tags($this -> studNum));

         //Bind values
         $statement -> bindParam(":name", $this -> studName);
         $statement -> bindParam(":age", $this -> studAge);
         $statement -> bindParam(":number", $this -> studNum);

         //Execute Query
         if ($statement -> execute()) {
             return true;
         }

         return false;
     }
 }



//Connect to Database
$database = new Database();
$db = $database -> createConnection();

$student = new Student($db);


if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    //Grab the new student data
    $data = json_decode(file_get_contents("php://input"));

    //Validate the required data is present
    if (!empty($data -> id) && !empty($data -> student_name) && !empty($data -> student_age) && !empty($data -> student_number)) {
        $student -> id = $data -> id;
        $student -> studName = $data -> student_name;
        $student -> studAge = $data -> student_age;
        $student -> studNum = $data -> student_number;

        if ($student -> update_student()) {
            http_response_code(200);
            echo json_encode(array("Message: " => "Student information successfully updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("Message: " => "Unable to update student information on database."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("Message: " => "Required data missing. Failed to update student information."));
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    //Grab the new student data
    $data = json_decode(file_get_contents("php://input"));

    //Validate the required data is present
    if (!empty($data -> id)) {
        $student -> id = $data -> id;

        if ($student -> delete_student()) {
            http_response_code(200);
            echo json_encode(array("Message: " => "Successfully deleted student"));
        } else {
            http_response_code(503);
            echo json_encode(array("Message: " => "Unable to delete student. Check 'id' and try again."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("Message: " => "Required 'id' data missing. Failed to delete student."));
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    //Query for students
    $statement = $student -> get_students();
    //Count the # of Rows returned
    $count = $statement -> rowCount();

    //Confirm there are records
    if ($count > 0) {
        //Create an array to hold the results in
        $students = array();

        while ($row = $statement -> fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $item = array(
        "id" => $id,
        "student_name" => $student_name,
        "student_number" => $student_number,
        "student_age" => $student_age,
      );

            array_push($students, $item);
        }

        http_response_code(200);

        echo json_encode($students);
    } else {
        http_response_code(404);

        echo json_encode(
            array("Message" => "No students in records.")
        );
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Grab the new student data
    $data = json_decode(file_get_contents("php://input"));

    //Validate the required data is present
    if (!empty($data -> student_name) && !empty($data -> student_age) && !empty($data -> student_number)) {
        $student -> studName = $data -> student_name;
        $student -> studAge = $data -> student_age;
        $student -> studNum = $data -> student_number;

        if ($student -> add_student()) {
            http_response_code(201);
            echo json_encode(array("Message: " => "Student successfully added to database."));
        } else {
            http_response_code(503);
            echo json_encode(array("Message: " => "Unable to add student to database."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("Message: " => "Required data missing. Failed to create new student."));
    }
}

?>
