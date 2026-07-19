<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function index()
    {
        return view('shipments.index', [
            'shipments' => Shipment::latest('ship_date')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('shipments.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:inbound,outbound,transfer'],
            'origin' => ['nullable', 'string'],
            'destination' => ['nullable', 'string'],
            'status' => ['required', 'in:pending,in_transit,delivered'],
            'ship_date' => ['nullable', 'date'],
        ]);
        $data['reference'] = 'SHP-'.str_pad((string) (Shipment::max('id') + 1), 4, '0', STR_PAD_LEFT);

        Shipment::create($data);

        return redirect('/shipments')->with('status', 'Shipment created.');
    }

    public function updateStatus(Request $request, Shipment $shipment)
    {
        $data = $request->validate(['status' => ['required', 'in:pending,in_transit,delivered']]);
        $shipment->update($data);

        return back()->with('status', 'Shipment status updated.');
    }
}
