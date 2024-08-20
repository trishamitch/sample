<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple AngularJS App</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
</head>
<body ng-app="myApp" ng-controller="MainController">
    <div class="container">
        <h1>Item List</h1>
        <form ng-submit="addItem()">
            <input type="text" ng-model="newItem" placeholder="Enter new item" required>
            <button type="submit">Add Item</button>
        </form>
        <ul>
            <li ng-repeat="item in items">{{ item }}</li>
        </ul>
    </div>
    <script>
        var app = angular.module('myApp', []);

        app.controller('MainController', function($scope, $http) {
            $scope.items = [];
            $scope.newItem = '';

            // Fetch all items from the server
            $scope.getItems = function() {
                $http.get('index.php?action=get')
                    .then(function(response) {
                        $scope.items = response.data;
                    }, function(error) {
                        console.error('Error fetching items:', error);
                    });
            };

            // Add a new item to the server
            $scope.addItem = function() {
                if ($scope.newItem) {
                    $http.post('index.php?action=add', { name: $scope.newItem }, {
                        headers: { 'Content-Type': 'application/json' }
                    })
                    .then(function(response) {
                        console.log('Item added:', response.data);
                        $scope.getItems();  // Refresh the list
                        $scope.newItem = '';  // Clear the input field
                    })
                    .catch(function(error) {
                        console.error('Error adding item:', error);
                    });
                }
            };

            // Load the items when the app starts
            $scope.getItems();
        });
    </script>

    <?php
    // PHP code to handle requests
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sampledb";

    // Create a connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Determine the action based on the query parameter
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $name = $conn->real_escape_string($input['name']);

        $sql = "INSERT INTO items (name) VALUES ('$name')";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["message" => "Item added successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
        }
    } elseif ($action === 'get' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $sql = "SELECT * FROM items";
        $result = $conn->query($sql);

        $items = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row['name'];
            }
        }

        echo json_encode($items);
    }

    $conn->close();
    ?>
</body>
</html>
