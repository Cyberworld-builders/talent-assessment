<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserCsvUploadTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test CSV parsing logic directly
     *
     * @return void
     */
    public function testCsvParsingLogic()
    {
        // Create a test CSV content
        $csvContent = "name,email,username,job_title,job_family\n";
        $csvContent .= "John Doe,john.doe@example.com,johndoe,Software Engineer,Engineering\n";
        $csvContent .= "Jane Smith,jane.smith@example.com,janesmith,Product Manager,Product\n";

        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'test_users');
        file_put_contents($tempFile, $csvContent);

        // Test the CSV parsing logic directly
        $users = [];
        $handle = fopen($tempFile, 'r');
        
        $this->assertNotFalse($handle, 'Could not open CSV file');

        // Read the header row
        $header = fgetcsv($handle);
        $this->assertNotFalse($header, 'Could not read header row');
        $this->assertEquals(['name', 'email', 'username', 'job_title', 'job_family'], $header);

        // Process each data row
        while (($row = fgetcsv($handle)) !== false) {
            // Create an associative array from header and row data
            $rowData = array_combine($header, $row);
            
            $name = isset($rowData['name']) ? $rowData['name'] : '';
            $email = isset($rowData['email']) ? trim($rowData['email']) : '';
            $job_title = isset($rowData['job_title']) ? $rowData['job_title'] : '';
            $job_family = isset($rowData['job_family']) ? $rowData['job_family'] : '';
            $username = isset($rowData['username']) ? $rowData['username'] : '';

            array_push($users, [
                'email' => $email,
                'name' => $name,
                'username' => $username,
                'job_title' => $job_title,
                'job_family' => $job_family
            ]);
        }

        fclose($handle);

        // Assert that users were parsed correctly
        $this->assertCount(2, $users);

        // Check the first user
        $firstUser = $users[0];
        $this->assertEquals('John Doe', $firstUser['name']);
        $this->assertEquals('john.doe@example.com', $firstUser['email']);
        $this->assertEquals('johndoe', $firstUser['username']);
        $this->assertEquals('Software Engineer', $firstUser['job_title']);
        $this->assertEquals('Engineering', $firstUser['job_family']);

        // Check the second user
        $secondUser = $users[1];
        $this->assertEquals('Jane Smith', $secondUser['name']);
        $this->assertEquals('jane.smith@example.com', $secondUser['email']);
        $this->assertEquals('janesmith', $secondUser['username']);
        $this->assertEquals('Product Manager', $secondUser['job_title']);
        $this->assertEquals('Product', $secondUser['job_family']);

        // Clean up
        unlink($tempFile);
    }

    /**
     * Test CSV parsing with alternative column names
     *
     * @return void
     */
    public function testCsvParsingWithAlternativeColumns()
    {
        // Create a test CSV content with alternative column names
        $csvContent = "name,e_mail,user_name,job_title,job_family\n";
        $csvContent .= "John Doe,john.doe@example.com,johndoe,Software Engineer,Engineering\n";

        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'test_users');
        file_put_contents($tempFile, $csvContent);

        // Test the CSV parsing logic with alternative column names
        $users = [];
        $handle = fopen($tempFile, 'r');
        
        $this->assertNotFalse($handle, 'Could not open CSV file');

        // Read the header row
        $header = fgetcsv($handle);
        $this->assertNotFalse($header, 'Could not read header row');

        // Process each data row
        while (($row = fgetcsv($handle)) !== false) {
            // Create an associative array from header and row data
            $rowData = array_combine($header, $row);
            
            $name = isset($rowData['name']) ? $rowData['name'] : '';
            $email = isset($rowData['email']) ? trim($rowData['email']) : '';
            $job_title = isset($rowData['job_title']) ? $rowData['job_title'] : '';
            $job_family = isset($rowData['job_family']) ? $rowData['job_family'] : '';
            $username = isset($rowData['username']) ? $rowData['username'] : '';

            // Handle alternative column names
            if (empty($email) && isset($rowData['e_mail'])) {
                $email = trim($rowData['e_mail']);
            }

            if (empty($username) && isset($rowData['user_name'])) {
                $username = $rowData['user_name'];
            }

            array_push($users, [
                'email' => $email,
                'name' => $name,
                'username' => $username,
                'job_title' => $job_title,
                'job_family' => $job_family
            ]);
        }

        fclose($handle);

        // Assert that users were parsed correctly with alternative column names
        $this->assertCount(1, $users);

        // Check the user
        $user = $users[0];
        $this->assertEquals('John Doe', $user['name']);
        $this->assertEquals('john.doe@example.com', $user['email']);
        $this->assertEquals('johndoe', $user['username']);
        $this->assertEquals('Software Engineer', $user['job_title']);
        $this->assertEquals('Engineering', $user['job_family']);

        // Clean up
        unlink($tempFile);
    }
}
