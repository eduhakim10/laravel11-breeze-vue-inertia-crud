<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Resources\CompanyResource;

use Illuminate\Support\Facades\DB; 

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::all(); // Fetch companies from the database
  
        return Inertia::render('Company/Index', [
            'companies' => $companies, // Pass companies to the Vue component
            'flash' =>[
                'success' => session('success'),
            ]
        ]);
      
 

    }

    public function datatables(Request $request){

        $companies = Company::orderBy('created_at', 'desc')->paginate(10);
        return CompanyResource::collection($companies);
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    
        return Inertia::render('Company/Create', [
            'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        $validatedData = $request->validated(); // This automatically validates the request

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validatedData['logo'] = $request->file('logo')->store('logos', 'public');
        }
        // Create company record
     
        try {
            $company = Company::create($validatedData);
    
            // Redirect to the index page with a success message
            return redirect()->route('companies.index')->with('success', 'Company created successfully!');
        } catch (\Exception $e) {
            return Inertia::back()->withErrors(['general' => 'An error occurred while creating the company.']);
        }
      //  return redirect()->route('companies.index')->with('success', 'Company created successfully.');


    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
       
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCompanyRequest $request,  $id)
    {
 
        $company = Company::find($id);
        if (!$company) {
            return response()->json(['message' => 'Company not found.'], 404);
        }
        $validated= $request->validated(); 
       
        if ($request->hasFile('logo')) {
            // Delete the old logo if it exists
            if ($company->logo && \Storage::disk('public')->exists($company->logo)) {
                \Storage::disk('public')->delete($company->logo);
            }
    
            // Store the new logo
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $logoPath; // Add the logo path to the validated data
        }
       
    
        // Save the updated company
       // $company->save();
       $company->update($validated);


            // Return the updated company data with a success message
            return response()->json([
                'message' => 'Company updated successfully.',
                'company' => $company, // Return the updated company data
            ]);
       
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $company = Company::findOrFail($id);
            // Delete the company
            $company->delete();
    
            // Return success response
            return response()->json([
                'message' => 'Company deleted successfully.',
            ], 200); // HTTP 200 OK
        } catch (\Exception $e) {
            // Handle any potential errors
            return response()->json([
                'message' => 'Failed to delete the company.',
                'error' => $e->getMessage(),
            ], 500); // HTTP 500 Internal Server Error
        }
       
    }
}
