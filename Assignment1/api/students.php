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
                      " SET student_name = ?,
                            student_age = ?,
                            student_number = ?
                      WHERE id = ?";

         //Prepare Query
         $statement = $this -> connection -> prepare($query);

         //Sanitize inputs
         $this -> studName = htmlspecialchars(strip_tags($this -> studName));
         $this -> studAge = htmlspecialchars(strip_tags($this -> studAge));
         $this -> studNum = htmlspecialchars(strip_tags($this -> studNum));
         $this -> id = htmlspecialchars(strip_tags($this -> id));

         //Bind values
         $statement -> bind_param("siii", $this -> studName, $this -> studAge, $this -> studNum, $this -> id);


         //Execute Query
         if ($statement -> execute()) {
           if($statement -> affected_rows > 0)  {
             return true;
           }
           else {
             return false;
           }
         }

         return false;
     }


     public function delete_student()
     {
         //Create SQL Query
         $query = "DELETE FROM " . $this -> table .
                    " WHERE id = ?";

         //Prepare Query
         $statement = $this -> connection -> prepare($query);

         //Sanitize inputs
         $this -> studName = htmlspecialchars(strip_tags($this -> id));

         //Bind values
         $statement -> bind_param("i", $this -> id);

         //Execute Query
         if ($statement -> execute()) {
           if($statement -> affected_rows > 0)  {
             return true;
           }
           else {
             return false;
           }
         }
         return false;
     }


     public function get_students()
     {
         //Create SQL Query
         $query = "SELECT * FROM " . $this -> table;
         //Prepare Query
         $statement = $this -> connection -> query($query);

         return $statement;
     }


     public function get_student($id = 0)
     {
         //Create SQL Query
         $query = "SELECT * FROM " . $this -> table .
                      " WHERE id = ?";

         //Prepare Query
         $statement = $this -> connection -> prepare($query);

         //Sanitize inputs
         $this -> id = htmlspecialchars(strip_tags($this -> id));


         //Bind values
         $statement -> bind_param("i", $id);

         //Execute Query
         if ($statement -> execute()) {
             $result = $statement -> get_result();
             return $result;
         } else {
             return null;
         }
     }


     public function insert_student()
     {
         //Create SQL Query
         $query = "INSERT INTO " . $this -> table .
                      " SET student_name = ?,
                           student_age = ?,
                           student_number = ?";

         //Prepare Query
         $statement = $this -> connection -> prepare($query);

         //Sanitize inputs
         $this -> studName = htmlspecialchars(strip_tags($this -> studName));
         $this -> studAge = htmlspecialchars(strip_tags($this -> studAge));
         $this -> studNum = htmlspecialchars(strip_tags($this -> studNum));

         //Bind values
         $statement -> bind_param("sis", $this -> studName, $this -> studAge, $this -> studNum);
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
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['QUERY_STRING'] == '') {
    //Query for students
    $statement = $student -> get_students();
    //Count the # of Rows returned
    $count = $statement -> num_rows;

    //Confirm there are records
    if ($count > 0) {
        //Create an array to hold the results in
        $students = array();

        while ($row = $statement -> fetch_assoc()) {
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
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $student -> get_student($id);
    //Count the # of Rows returned
    $count = $result -> num_rows;
    //Confirm a result was returned
    if ($count > 0) {
        //Confirm there is only one student with that id
        if ($count === 1) {
            extract($result -> fetch_assoc());
            $student -> id = $id;
            $student -> studName = $student_name;
            $student -> studNum = $student_number;
            $student -> studAge = $student_age;
            http_response_code(200);
            echo json_encode($student);
        } else {
            http_response_code(500);
            echo json_encode(
                array("Message" => "Request returned wrong number of results. Check 'id' and try again.")
            );
        }
    } else {
        http_response_code(404);

        echo json_encode(
            array("Message" => "No student found with matching id. Check 'id' and try again.")
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

        if ($student -> insert_student()) {
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
