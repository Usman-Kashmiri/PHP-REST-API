<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");

include 'config.php';
$objDb = new DbConnect;
$conn = $objDb->connect();

$method = $_SERVER['REQUEST_METHOD'];
switch($method) {
    case "GET":
        $sql = "SELECT * FROM contactlist";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if(isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE c_id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $path[3]);
            $stmt->execute();
            $contacts = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode($contacts);
        break;
    case "POST":
        $contact = json_decode( file_get_contents('php://input') );
        $sql = "INSERT INTO contactlist(c_id, first_name, last_name, phone_no, email_address, image_path, contact_address) VALUES (:id, :fname, :lname, :mobile, :email, :img, :address)";
        $stmt = $conn->prepare($sql);
        // $created_at = date('Y-m-d');
        $stmt->bindParam(':id', $contact->c_id);
        $stmt->bindParam(':fname', $contact->first_name);
        $stmt->bindParam(':lname', $contact->last_name);
        $stmt->bindParam(':mobile', $contact->phone_no);
        $stmt->bindParam(':email', $contact->email_address);
        $stmt->bindParam(':img', $contact->image_path);
        $stmt->bindParam(':address', $contact->contact_address);

        if($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record inserted successfully.'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to insert record.'];
        }
        echo json_encode($response);
        break;

    case "PUT":
        $contact = json_decode( file_get_contents('php://input') );
        $sql = "UPDATE contactlist SET first_name= :fname, last_name= :lname, phone_no= :mobile, email_address= :email, image_path= :img, contact_address= :address WHERE c_id = :id";
        $stmt = $conn->prepare($sql);
        // $updated_at = date('Y-m-d');
        $stmt->bindParam(':id', $contact->c_id);
        $stmt->bindParam(':fname', $contact->first_name);
        $stmt->bindParam(':lname', $contact->last_name);
        $stmt->bindParam(':mobile', $contact->phone_no);
        $stmt->bindParam(':email', $contact->email_address);
        $stmt->bindParam(':img', $contact->image_path);
        $stmt->bindParam(':address', $contact->contact_address);

        if($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record updated successfully.'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to update record.'];
        }
        echo json_encode($response);
        break;

    case "DELETE":
        $sql = "DELETE FROM contactlist WHERE c_id = :id";
        $path = explode('/', $_SERVER['REQUEST_URI']);

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $path[3]);

        if($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record deleted successfully.'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to delete record.'];
        }
        echo json_encode($response);
        break;
}