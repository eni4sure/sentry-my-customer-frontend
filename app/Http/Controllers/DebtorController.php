<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class DebtorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $store_url = env('API_URL', 'https://dev.api.customerpay.me') . '/store';

        $cl = new Client;
        $payloader = ['headers' => ['x-access-token' => Cookie::get('api_token')]];

        $store_resp = $cl->request("GET", $store_url, $payloader);
        $statsCode = $store_resp->getStatusCode();
        $store_response = $store_resp->getBody();
        $Stores = json_decode($store_response);

        $url = env('API_URL', 'https://dev.api.customerpay.me') . '/debt';

        try {

            $client = new Client();
            $payload = ['headers' => ['x-access-token' => Cookie::get('api_token')]];

            $response = $client->request("GET", $url, $payload);
            $statusCode = $response->getStatusCode();

            if ($statusCode == 200 && $statsCode == 200) {
                $body = $response->getBody();
                $result = json_decode($body);
                $debtors = $result->data->debts;
                $stores = $Stores->data->stores;

                return view('backend.debtor.index', compact('debtors', 'stores'));
            }

            Session::flash('message', "Temporarily unable to get all stores");
            $debtors = [];
            $stores = [];
            return view('backend.debtor.index',  compact('debtors', 'stores'));
        } catch (RequestException $e) {
            Log::info('Catch error: DebtorController - ' . $e->getMessage());
            if ($e->getCode() == 401) {
                Session::flash('message', 'session expired');
                return redirect()->route('logout');
            }

            if ($e->hasResponse()) {
                $response = $e->getResponse()->getBody();
                $result = json_decode($response);
                Session::flash('message', $result->message);
                $debtors = [];
                $stores = [];
                return view('backend.debtor.index',  compact('debtors', 'stores'));
            }

            //5xx server error
            return view('errors.500');
        } catch (\Exception $e) {
            Log::error('Catch error: DebtorController - ' . $e->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $store_url = env('API_URL', 'https://dev.api.customerpay.me') . '/store';
        $transaction_url = env('API_URL', 'https://dev.api.customerpay.me') . '/transaction';

        $cl = new Client;
        $cl2 = new Client;

        $payloader = ['headers' => ['x-access-token' => Cookie::get('api_token')]];

        $resp = $cl->request("GET", $store_url, $payloader);
        $response = $cl2->request("GET", $transaction_url, $payloader);

        $statsCode = $resp->getStatusCode();
        $statsCode2 = $response->getStatusCode();

        $body_response = $resp->getBody();
        $body_response2 = $response->getBody();

        $Stores = json_decode($body_response);
        $transaction = json_decode($body_response2);

        if ($statsCode == 200) {
            return view('backend.debtor.create', compact('transaction'))->with('response', $Stores->data->stores);
        } else if ($statsCode == 500) {
            return view('errors.500');
        }
        // return view('backend.debtor.create');
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        return view('backend.debtor.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
    }

    public function search(Request $request)
    {
        $id = $request->store_id;
        $url = env('API_URL', 'https://dev.api.customerpay.me/') . '/debt/' . $id;
        $store_url = env('API_URL', 'https://dev.api.customerpay.me') . '/store';

        try {
            $cl = new Client;
            $payloader = ['headers' => ['x-access-token' => Cookie::get('api_token')]];
            $store_resp = $cl->request("GET", $store_url, $payloader);
            $statsCode = $store_resp->getStatusCode();
            $store_response = $store_resp->getBody();
            $Stores = json_decode($store_response);

            $client = new Client();
            $payload = ['headers' => ['x-access-token' => Cookie::get('api_token')]];
            $response = $client->request("GET", $url, $payload);
            $statusCode = $response->getStatusCode();

            if ($statusCode == 200 && $statsCode == 200) {
                $body = $response->getBody();
                $result = json_decode($body);
                $debtors = $result->data->debts;
                $stores = $Stores->data->stores;

                return view('backend.debtor.index', compact('debtors', 'stores'));
            }

            Session::flash('message', "Temporarily unable to get all stores");
            $debtors = [];
            $stores = [];
            return view('backend.debtor.index',  compact('debtors', 'stores'));
        } catch (RequestException $e) {
            Log::info('Catch error: DebtorController - ' . $e->getMessage());

            if ($e->getCode() == 401) {
                Session::flash('message', 'session expired');
                return redirect()->route('logout');
            }

            if ($e->hasResponse()) {
                $response = $e->getResponse()->getBody();
                $result = json_decode($response);
                Session::flash('message', $result->message);
                $debtors = [];
                $stores = [];
                return view('backend.debtor.index',  compact('debtors', 'stores'));
            }

            //5xx server error
            return view('errors.500');
        } catch (\Exception $e) {
            Log::error('Catch error: StoreController - ' . $e->getMessage());
            return view('errors.500');
        }
    }

    public function sendReminder(Request $request)
    {
        $_id = $request->transaction_id;
        $message = $request->message;

        $url = env('API_URL', 'https://dev.api.customerpay.me') . '/debt/send';

        try {
            $client =  new Client();
            $payload = [
                'headers' => ['x-access-token' => Cookie::get('api_token')],
                'form_params' => [
                    'transaction_id' => $_id,
                    'message' => $message,
                ],
            ];

            $response = $client->request("POST", $url, $payload);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody();
            $data = json_decode($body);

            if ($statusCode == 200 && $data->success) {
                // return \
                $request->session()->flash('alert-class', 'alert-success');
                Session::flash('message', $data->Message);

                return redirect()->back();
            } else {
                $request->session()->flash('alert-class', 'alert-waring');
                // Session::flash('message', $data->message);
                return redirect()->back();
            }
        } catch (RequestException $e) {
            Log::info('Catch error: DebtorController - ' . $e->getMessage());

            if ($e->hasResponse()) {
                if ($e->getCode() == 401) {
                    Session::flash('message', 'session expired');
                    return redirect()->route('logout');
                }

                $response = $e->getResponse()->getBody();
                $result = json_decode($response);
                Session::flash('message', $result->Message);
                $debtors = [];
                $stores = [];
                return view('backend.debtor.index',  compact('debtors', 'stores'));
            }

            //5xx server error
            return view('errors.500');
        } catch (\Exception $e) {
            Session::flash('message', $e->getMessage());
            Log::error(' ' . $e->getMessage());
            return view('errors.500');
        }
    }

    public function sheduleReminder(Request $request)
    {

        $request->validate([
            'transaction_id' => 'required',
            'scheduleDate' => 'required',
            'time' =>  'required',
        ]);

        $url = env('API_URL', 'https://dev.api.customerpay.me') . '/debt/schedule';


        try {
            $client =  new Client();
            $payload = [
                'headers' => ['x-access-token' => Cookie::get('api_token')],
                'form_params' => [
                    'scheduleDate' => $request->scheduleDate,
                    'time' => $request->time,
                    'transaction_id' => $request->transaction_id,
                    'message' => $request->message,
                ],
            ];
            $response = $client->request("POST", $url, $payload);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody();
            $data = json_decode($body);

            if ($statusCode == 200  && $data->success) {
                $request->session()->flash('alert-class', 'alert-success');
                Session::flash('message', $data->Message);

                return back();
            } else {
                $request->session()->flash('alert-class', 'alert-success');
                return redirect()->back();
            }
        } catch (RequestException $e) {
            Log::info('Catch error: DebtorController - ' . $e->getMessage());

            if ($e->getCode() == 401) {
                Session::flash('message', 'session expired');
                return redirect()->route('logout');
            }

            if ($e->hasResponse()) {
                $response = $e->getResponse()->getBody();
                $result = json_decode($response);
                Session::flash('message', $result->Message);
                $debtors = [];
                $stores = [];
                return view('backend.debtor.index',  compact('debtors', 'stores'));
            }

            //5xx server error
            return view('errors.500');
        } catch (\Exception $e) {
            Session::flash('message', $e->getMessage());
            Log::error(' ' . $e->getMessage());
            return view('errors.500');
        }
    }

    public function markPaid(Request $request, $id)
    {

        $url = env('API_URL', 'https://dev.api.customerpay.me') . '/debt/update/' . $id;

        try {
            $client =  new Client();
            $payload = [
                'headers' => ['x-access-token' => Cookie::get('api_token')],
            ];

            $response = $client->request("PUT", $url, $payload);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody();
            $data = json_decode($body);

            if ($statusCode == 200  && $data->success) {
                $request->session()->flash('alert-class', 'alert-success');
                Session::flash('message', $data->message);

                return back();
            } else {
                $request->session()->flash('alert-class', 'alert-waring');
                Session::flash('message', $data->message);
                return redirect()->back();
            }
        } catch (RequestException $e) {
            Log::info('Catch error: DebtorController - ' . $e->getMessage());

            if ($e->getCode() == 401) {
                Session::flash('message', 'session expired');
                return redirect()->route('logout');
            }

            if ($e->hasResponse()) {
                $response = $e->getResponse()->getBody();
                $result = json_decode($response);
                Session::flash('message', $result->message);
                return view('backend.debtor.index', []);
            }

            //5xx server error
            return view('errors.500');
        } catch (\Exception $e) {
            Session::flash('message', $e->getMessage());
            Log::error(' ' . $e->getMessage());
            return view('errors.500');
        }
    }
}
