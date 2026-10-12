<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\Attachment;

class CompanyController extends Controller
{
    public function index()
    {
        return response()->json(Company::latest()->get());
    }

    public function store(StoreCompanyRequest $request)
    {
        $company = Company::create($request->validated());

        if ($request->filled('attachment_token')) {
            Attachment::claim($request->attachment_token, $company);
        }

        return response()->json($company, 201);
    }

    public function show(Company $company)
    {
        $company->load('attachments.uploader:id,name');
        return response()->json($company);
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $company->update($request->validated());
        return response()->json($company);
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return response()->json(null, 204);
    }
}