<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceStoreRequest;
use App\Http\Requests\Admin\ServiceUpdateRequest;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $services = Service::paginate(10);

        return view('admin.services.index', compact('services'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $service = new Service();

        return view('admin.services.create', compact('service'));
    }

    /**
     * @param \App\Http\Requests\Admin\ServiceStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServiceStoreRequest $request)
    {
        $service = new Service($request->validated());

        $service->save();

        return redirect()
            ->route('admin.services.index')
            ->with(['alert-success' => 'Serviço criado com sucesso!']);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Service $service
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Service $service)
    {
        return view('admin.services.show', compact('service'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Service $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * @param \App\Http\Requests\Admin\ServiceUpdateRequest $request
     * @param \App\Service $service
     * @return \Illuminate\Http\Response
     */
    public function update(ServiceUpdateRequest $request, Service $service)
    {
        $service->update($request->validated());

        return redirect()
            ->route('admin.services.index')
            ->with(['alert-success' => 'Serviço editado com sucesso!']);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Service $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Service $service)
    {
        $service->delete();

        return response()->json($service);
    }
}
