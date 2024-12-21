<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Resources\CompanyResource;


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

        $companies = Company::paginate(10);
        return CompanyResource::collection($companies);
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      //  dd(auth()->user()->email);

        return Inertia::render('Company/Create');
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
        Company::create($validatedData);
  
        return redirect()->route('companies.index')->with('success', 'Company created successfully.');


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
        return Inertia::render('Companies/Edit', [
            'company' => $company,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
        ]);
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        $company->update($request->all());

        return redirect()->route('companies.index')->with('success', 'Company updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('companies.index')->with('success', 'Company deleted successfully.');
    }
}
