<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RandomUserService;
use Illuminate\Support\Facades\App;

/**
 * Class RandomUserController
 * @package App\Http\Controllers
 */
class RandomUserController extends Controller
{
    /**
     * Fetch random users, sort them, and return as XML.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userService = App::make(RandomUserService::class);
        $validator = validator()->make($request->all(), [
            'count' => 'required|integer|min:1',
        ], [
            'count.required' => 'The count field is required.',
            'count.integer' => 'The count field must be an integer.',
            'count.min' => 'The count field must be at least :min.',
        ]);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['error' => implode(' ', $errors)], 422);
        }
    
        $count = $request->input('count', 10);
        $limit = $request->input('limit');
        // Fetch random user data using the service
        $randomUsers = $userService->fetchRandomUsers($count);
        if (!$randomUsers) {
            return response()->json(['error' => 'Failed to fetch random users'], 500);
        }
        // Sort users by last name using the service
        $sortedUsers = $userService->sortUsersByLastName($randomUsers);
        if ($limit > 0) {
            $sortedUsers = array_slice($sortedUsers, 0, $limit);
        }
        // Convert the data to XML using the service
        $xmlResponse = $userService->convertToXML($sortedUsers);

        return response($xmlResponse, 200)->header('Content-Type', 'application/xml');
    }
}
