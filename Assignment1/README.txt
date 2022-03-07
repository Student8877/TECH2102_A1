Testing
===================
API Address = http://localhost/TECH_2102_GLA/api/students.php
Walkthrough Video - https://bowvalley-my.sharepoint.com/:v:/g/personal/j_braaksma331_mybvc_ca/EWFqfjHBDEpIgHolJKRuD4kBBzzUnsGvi6GLdOB4Lf9e0Q
Github Repo - https://github.com/Student8877/TECH201_GLA_1/invitations


GET REQUEST (Returns all students)
==================================
1.Send GET Request to API Address.
2.If successful, API returns 200 code and JSON response containing array of all student data from database.
3.If failed, API returns appropriate error code with message.


POST REQUEST (Adds a new student)
=================================
1.Send POST Request with following raw text body:
{
  "student_name" : "Test",
  "student_age" : 23,
  "student_number" : "111222"
}
Note: Change above values as needed for testing.
2.If successful, API returns 200 code and success message.
3.If failed, API returns appropriate error code with message.


PUT REQUEST (Modifies data for existing student)
================================================
1.Send PUT Request with following raw text body:
{
    "id" : 15,
    "student_name" : "UpdatedTest",
    "student_age" : 23,
    "student_number" : "111222"
}
Note: Change above values as needed for testing. "id" must match existing id in database.
      You can view existing entries in database following the GET request instructions above.
2.If successful, API returns 200 code and success message.
3.If failed, API returns appropriate error code with message.


DELETE REQUEST (Deletes existing student)
=========================================
1.Send DELETE Request with following raw text body:
{
    "id" : 15
}
Note: Change above values as needed for testing. "id" must match existing id in database.
      You can view existing entries in database following the GET request instructions above.
2.If successful, API returns 200 code and success message.
3.If failed, API returns appropriate error code with message.



All code written by Jamie Braaksma.
