var app = angular.module('myApp', []);

app.controller('MainController', function($scope, $http) {
    $scope.items = [];
    $scope.newItem = '';

    // Fetch all items from the server
    $scope.getItems = function() {
        $http.get('http://localhost/items.php')
            .then(function(response) {
                $scope.items = response.data;
            }, function(error) {
                console.error('Error fetching items:', error);
            });
    };

    // Add a new item to the server
    $scope.addItem = function() {
        if ($scope.newItem) {
            $http.post('http://localhost/items.php', { name: $scope.newItem })
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
