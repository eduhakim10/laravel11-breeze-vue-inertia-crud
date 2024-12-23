<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Company;
use App\Http\Requests\StoreEmployeeRequest;
use App\Notifications\EmployeeCreated;
use App\Http\Resources\EmployeeResource;


class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::all(); // Fetch companies from the database
        return Inertia::render('Employee/Index', [
            'companies' => $companies,
            'flash' =>[
                'success' => session('success'),
            ]
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       
        $companies = Company::all(); // Fetch companies from the database
        return Inertia::render('Employee/Create', [
            'companies' => $companies,
            'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
        ]);
      
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $validatedData = $request->validated();  
        Employee::create($validatedData);

       
     //   $company->user->notify(new EmployeeCreated($employee, $company));
        try {
            $employees = Employee::create($validatedData);

            // Redirect to the index page with a success message
            return redirect()->route('employees.index')->with('success', 'Employee created successfully!');
        } catch (\Exception $e) {
            return Inertia::back()->withErrors(['general' => 'An error occurred while creating the company.']);
        }
       // return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        //
    }

    public function datatables(Request $request){

        $employees = Employee::orderBy('created_at', 'desc')->paginate(10);
        return EmployeeResource::collection($employees);
    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreEmployeeRequest $request, $id)
    {
        $employees = Employee::find($id);
        if (!$employees) {
            return response()->json(['message' => 'Employee not found.'], 404);
        }
        $validated= $request->validated(); 

        return response()->json([
            'message' => 'Company updated successfully.',
            'company' => $employees, // Return the updated company data
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {

        try {
            $employees = Employee::findOrFail($id);
            // Delete the company
            $employees->delete();
    
            // Return success response
            return response()->json([
                'message' => 'Employee deleted successfully.',
            ], 200); // HTTP 200 OK
        } catch (\Exception $e) {
            // Handle any potential errors
            return response()->json([
                'message' => 'Employee to delete the company.',
                'error' => $e->getMessage(),
            ], 500); // HTTP 500 Internal Server Error
        }

    }
}
