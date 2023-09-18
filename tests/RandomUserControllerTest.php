
<?php
use Illuminate\Http\Request;
use App\Http\Controllers\RandomUserController;
use App\Services\RandomUserService;
use Illuminate\Foundation\Testing\TestCase;

class RandomUserControllerTest extends TestCase
{
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        return $app;
    }
    public function testIndexMethodReturnsXmlResponse()
    {
        // Create an instance of the RandomUserController with a mock of RandomUserService
        $userService = $this->createMock(RandomUserService::class);
        $this->app->instance(RandomUserService::class, $userService);

        // Create an instance of the RandomUserController
        $controller = $this->app->make(RandomUserController::class);

        // Define sample input data and expected output
        $count = 5; 
        $request = Request::create('/index', 'GET', ['count' => $count]);
        $sampleInput = [
            [
                'name' => 'Mr Roger Cooper',
                'phone' => '08-5993-1299',
                'email' => 'roger.cooper@example.com',
                'country' => 'Australia',
            ]
            
        ];
        
        $expectedOutput = '<?xml version="1.0"?>
        <users>
            <user>
                <name>Mr Roger Cooper</name>
                <phone>08-5993-1299</phone>
                <email>roger.cooper@example.com</email>
                <country>Australia</country>
            </user> 
        </users>';
        

        // Mock the behavior of RandomUserService methods
        $userService->expects($this->once())
            ->method('fetchRandomUsers')
            ->with($count)
            ->willReturn($sampleInput);

        $userService->expects($this->once())
            ->method('sortUsersByLastName')
            ->with($sampleInput)
            ->willReturn($sampleInput);

        $userService->expects($this->once())
            ->method('convertToXML')
            ->with($sampleInput)
            ->willReturn($expectedOutput);

            $response = $controller->index($request);

            $this->assertEquals(200, $response->getStatusCode());

            // Assert the response content type is XML
            $this->assertEquals('application/xml', $response->headers->get('Content-Type'));

            $this->assertStringContainsString('<name>Mr Roger Cooper</name>', $response->getContent());
    }
    public function testIndexMethodWithInvalidInput()
    {
        // Create an instance of the RandomUserController
        $controller = $this->app->make(RandomUserController::class);
    
        // Define invalid input data, like missing 'count' parameter
        $request = Request::create('/index', 'GET');
    
        // Call the index method and assert the response
        $response = $controller->index($request);
    
        // Assert the response status code for validation failure (e.g., 422)
        $this->assertEquals(422, $response->getStatusCode());
    
        // Assert that the response content contains specific validation error messages
        $this->assertStringContainsString('The count field is required.', $response->getContent());
    }
    
}
