<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MemorialRoom;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\BookingController;
use Illuminate\Database\Eloquent\Builder;
use App\Reference;
use App\BookingLineItems;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use File;
use Excel;
use App\Exports\BookingLogExport;
use Carbon\Carbon;

class MemorialRoomController extends Controller
{
    /**
     * @OA\Post(
     *     tags={"Memorial Room"},
     *     path="/api/memorial-room",
     *     summary="Create Memorial Room",
     *     operationId="store",
     *     @OA\Parameter(
     *         name="room_no",
     *         in="query",
     *         required=true,
     *         description="Room No",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="price_daily",
     *         in="query",
     *         required=true,
     *         description="Price Daily",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="price_hourly",
     *         in="query",
     *         required=true,
     *         description="Price Hourly",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=true,
     *         description="Status",
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error server",
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'room_no'          => 'required|unique:memorial_rooms',
            'price_daily'      => 'required',
            'price_hourly'     => 'required',
            'status'           => 'required',
        ]);

        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $memorialRoom = MemorialRoom::create($request->all());
        if ($memorialRoom) {
            $memorialRoom = MemorialRoom::with('status')->find($memorialRoom->id);
            return response()->json([
                'status' => 'Successfully Added Memorial Room',
                'data'   => $memorialRoom
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'errors' => "Something bad happened, please try later"
            ], 422);
        }
    }

    /**
     * @OA\Get(
     *     tags={"Memorial Room"},
     *     path="/api/memorial-room",
     *     summary="Get list Memorial Room",
     *     operationId="index",
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="limit page",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             minimum=1,
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="filter",
     *         in="query",
     *         description="Filter",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error server",
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */

    public function index(Request $request)
    {
        $limit = intval($request->query('limit'));
        $memorialRooms = MemorialRoom::where('id', '>', 0);
        $filter = json_decode($request->query('filter'));
        if (!empty($filter->all)) {
            $key_word = $filter->all;
            $memorialRooms->where(function ($query) use ($key_word) {
                $query->where('room_no',     'like', "%" . $key_word . "%")
                    ->orWhere('price_hourly', 'like', "%" . $key_word . "%")
                    ->orWhere('price_daily', 'like', "%" . $key_word . "%")
                    ->orWhereHas('status', function (Builder $query) use ($key_word) {
                        $query->where(
                            [
                                ['reference_type', '=', 'admin_room_status'],
                                ['reference_value_text', 'like', '%' . $key_word . '%'],
                            ]
                        );
                    });
            });
        }
        if (!empty($filter->room_no)) {
            $memorialRooms->where('room_no', 'like', '%' . $filter->room_no . '%');
        }
        if (!empty($filter->price_hourly)) {
            $memorialRooms->where('price_hourly', 'like', '%' . $filter->price_hourly . '%');
        }
        if (!empty($filter->price_daily)) {
            $memorialRooms->where('price_daily', 'like', '%' . $filter->price_daily . '%');
        }
        if (!empty($filter->status)) {
            $status = $filter->status;
            $memorialRooms->whereHas('status', function (Builder $query) use ($status) {
                $query->where(
                    [
                        ['reference_type', '=', 'admin_room_status'],
                        ['reference_value_text', 'like', $status . '%'],
                    ]
                );
            });
        }
        $memorialRooms = $memorialRooms->with('status')->paginate($limit)->toArray();
        $memorialRooms['status'] = "success";
        return response()->json($memorialRooms, 200);
    }

    /**
     * @OA\Delete(
     *     tags={"Memorial Room"},
     *     path="/api/memorial-room",
     *     summary="Delete Memorial Room",
     *     operationId="delete",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="ids",
     *                     description="ID Memorial Room",
     *                     type="string",
     *                     default="[1,2,3]"
     *                  ),
     *                  required={"ids"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error server",
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */

    public function delete(Request $request)
    {
        $ids = $request->ids;
        if (is_string($ids)) {
            $ids = json_decode($ids);
        }
        $memorialRooms = MemorialRoom::whereIn('id', $ids)->delete();

        if ($memorialRooms) {
            return response()->json(
                [
                    'status' => 'Successfully Deleted Memorial Room',
                ],
                200
            );
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find room'
            ],
            404
        );
    }

    /**
     * @OA\Put(
     *     tags={"Memorial Room"},
     *     path="/api/memorial-room/{id}",
     *     summary="Update Memorial Room",
     *     operationId="update",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Id Memorial Room",
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="room_no",
     *         in="query",
     *         required=true,
     *         description="Room No",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="price_daily",
     *         in="query",
     *         required=true,
     *         description="Price Daily",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="price_hourly",
     *         in="query",
     *         required=true,
     *         description="Price Hourly",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=true,
     *         description="Status",
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error server",
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */

    public function update(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            'room_no'          => 'required',
            'price_daily'      => 'required',
            'price_hourly'     => 'required',
            'status'           => 'required',
        ]);

        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $memorialRoom = MemorialRoom::where('id', $id)->first();
        if (!$memorialRoom) {
            return response()->json([
                'status' => 'error',
                'errors' => "Cannot find room"
            ], 404);
        }
        $memorialRoom = $memorialRoom->update($request->all());
        if ($memorialRoom) {
            $memorialRoom = MemorialRoom::with('status')->find($id);
            return response()->json([
                'status' => 'Successfully Update Memorial Room',
                'data'   => $memorialRoom
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'errors' => "Something bad happened, please try later"
            ], 422);
        }
    }

    /**
     * @OA\Get(
     *     tags={"Memorial Room"},
     *     path="/api/memorial-room/{id}",
     *     summary="Get detail Memorial Room",
     *     operationId="detail",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID Memorial Room",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             minimum=1,
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error server",
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */

    public function detail($id)
    {
        $memorialRoom = MemorialRoom::with('status')->find($id);

        if ($memorialRoom) {
            return response()->json(
                [
                    'status' => 'success',
                    'data' => $memorialRoom->toArray()
                ],
                200
            );
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find room'
            ],
            404
        );
    }

    /**
     * @OA\Get(
     *     tags={"Memorial Room"},
     *     path="/api/list-room",
     *     summary="Get list Memorial Room Not Booking",
     *     operationId="listRoomNotBooking",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error server",
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */

    public function listRoomNotBooking(Request $request)
    {
        $queryOrder = "CASE WHEN room_no = 'Chapel' THEN 1 ";
        $queryOrder .= "WHEN room_no = 'Parousia' THEN 2 ";
        $queryOrder .= "WHEN room_no = 'Hallelujah' THEN 3 ";
        $queryOrder .= "WHEN room_no = 'Glory Lounge' THEN 4 ";
        $queryOrder .= "WHEN room_no = 'Eternity' THEN 5 ";
        $queryOrder .= "ELSE 6 END";

        $status = Reference::where('reference_type', 'admin_room_status')
            ->where('reference_value_text', 'Available')
            ->whereNull('deleted_at')->first();
        $memorialRooms = MemorialRoom::where('status', $status->id)->whereNull('booking_id')->with('status')->orderByRaw($queryOrder);
        return response()->json(
            [
                'status' => 'success',
                'data' => $memorialRooms->get()
            ],
            200
        );
    }

    /**
     * @OA\Get(
     *     tags={"Memorial Room"},
     *     path="/api/booking-log/{id}",
     *     summary="Get list Booking Log",
     *     operationId="getBookingLog",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Id Memorial Room",
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="limit page",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             minimum=1,
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="filter",
     *         in="query",
     *         description="Filter",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error server",
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */

    public function getBookingLog(Request $request, $id)
    {
        $limit = intval($request->query('limit'));
        $filter = json_decode($request->query('filter'));
        $booking_type = Reference::where('reference_type', 'booking_type')
            ->where('reference_value_text', 'Memorial Rooms')
            ->whereNull('deleted_at')->first();
        $booking_log = BookingLineItems::where('booking_type_id', $booking_type->id)->where('service_id', $id)->with('client', 'funeral_director', 'status')->whereNull('deleted_at');
        ///
        $type_1 = "d/m/Y";
        $type_2 = "d/m";
        $type_expectations_1 = "Y-m-d";
        $type_expectations_2 = "m-d";
        ///
        if (!empty($filter->all)) {
            $key_word = $filter->all;
            //
            $key_word = BookingController::custom_date($type_1, $type_2, $key_word, $type_expectations_1, $type_expectations_2);
            //
            $booking_log->where(function ($query) use ($key_word) {
                $query->whereHas('client', function (Builder $query) use ($key_word) {
                    $query->where('display_name', 'like', '%' . $key_word . '%');
                })
                    ->orWhereHas('funeral_director', function (Builder $query) use ($key_word) {
                        $query->where('company_name', 'like', '%' . $key_word . '%');
                    })
                    ->orWhere('booking_date', 'like', '%' . $key_word . '%')
                    ->orWhere('check_in_date', 'like', '%' . $key_word . '%')
                    ->orWhere('check_out_date', 'like', '%' . $key_word . '%')
                    ->orWhere('id',               'like', "%" . $key_word . "%");
            });
        }
        if (!empty($filter->booked_by)) {
            $booked_by = $filter->booked_by;
            $booking_log->whereHas('client', function (Builder $query) use ($booked_by) {
                $query->where('display_name', 'like', '%' . $booked_by . '%');
            });
        }
        if (!empty($filter->booked_date)) {
            $booked_date = $filter->booked_date;
            $booked_date = BookingController::custom_date($type_1, $type_2, $booked_date, $type_expectations_1, $type_expectations_2);
            $booking_log->where('booking_date', 'like', '%' . $booked_date . '%');
        }
        if (!empty($filter->check_in)) {
            $check_in = $filter->check_in;
            $check_in = BookingController::custom_date($type_1, $type_2, $check_in, $type_expectations_1, $type_expectations_2);
            $booking_log->where('check_in_date', 'like', '%' . $check_in . '%');
        }
        if (!empty($filter->check_out)) {
            $check_out = $filter->check_out;
            $check_out = BookingController::custom_date($type_1, $type_2, $check_out, $type_expectations_1, $type_expectations_2);
            $booking_log->where('check_out_date', 'like', '%' . $check_out . '%');
        }
        if (!empty($filter->funeral_director)) {
            $funeral_director = $filter->funeral_director;
            $booking_log->whereHas('funeral_director', function (Builder $query) use ($funeral_director) {
                $query->where('company_name', 'like', '%' . $funeral_director . '%');
            });
        }
        if (!empty($filter->id)) {
            $id = $filter->id;
            $booking_log->where('id', 'like', '%' . $id . '%');
        }


        $booking_log = $booking_log->paginate($limit)->toArray();
        $booking_log['status'] = 'success';

        return response()->json($booking_log, 200);
    }

    /**
     * @OA\Post(
     *     tags={"Memorial Room"},
     *     path="/api/export-booking-log",
     *     summary="Export Booking Log",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="ids",
     *                     description="ID Memorial Room",
     *                     type="string",
     *                     default="[1,2,3]"
     *                  ),
     *                  required={"ids"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error server",
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function exportBookingLog(Request $request){
        $ids = $request->ids;
        if (is_string($ids)) {
            $ids = json_decode($ids);
        }
        return Excel::download(new BookingLogExport($ids), 'booking_log.csv');
    }

    /**
     * @OA\Post(
     *     tags={"Memorial Room"},
     *     path="/api/import-room",
     *     summary="Import file Room excel",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="file_excel",
     *                     description="file_excel Room",
     *                     type="file",
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error server",
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    //import file
    public function importListRoom(Request $request)
    {
        ini_set('max_execution_time', 3000);
        $file = $request->file("file_excel");
        if (!empty($file)) {
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->setShouldFormatDates(true);
            $reader->open($file);
            if (!empty($reader)) {
                foreach ($reader->getSheetIterator() as $sheet) {
                    if ($sheet->getName() == "Facility") {
                        foreach ($sheet->getRowIterator() as $row) {
                            $cells = $row->getCells();
                            //
                            $room_no                = $cells[0]->getValue();
                            $price_daily            = $cells[6]->getValue();
                            $price_hourly           = $cells[7]->getValue();
                            //
                            if (!($room_no === "Facility")) {
                                if (!($room_no === " " || $room_no == null)) {
                                    $type = Reference::where(
                                        [
                                            ['reference_type', '=', 'admin_room_status'],
                                            ['reference_value_text', '=', 'Available'],
                                        ]
                                    )->first();
                                    //
                                    $room = MemorialRoom::where('room_no', '=', $room_no)->first();
                                    if (!($room != null)) {
                                        MemorialRoom::create([
                                            'room_no'       => $room_no,
                                            'price_daily'   => $price_daily,
                                            'price_hourly'  => $price_hourly,
                                            'status'        => $type->id
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
                //
                $path = public_path() . '/import/MemorialRoom/';
                if (File::makeDirectory($path, 0777, true, true)) {
                    $name_file = $file->getClientOriginalName();
                    $file->move($path, $name_file);
                } else {
                    $name_file = $file->getClientOriginalName();
                    $file->move($path, $name_file);
                }

                //  

                return response()->json(
                    [
                        'status' => 'Successfully import file'
                    ],
                    200
                );
                // 
                $reader->close();
            }
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => 'Cannot import file'
                ],
                404
            );
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Not file'
            ],
            404
        );
    }

    public function getRoomInfo($id){
        $nowDate = Carbon::now()->toDateString();
        $nowTime = Carbon::now()->toTimeString();
        $booking_room = BookingLineItems::where('check_in_date', '<=', $nowDate)
        ->where('check_out_date', '>=', $nowDate)
        ->where('check_in_time', '<', $nowTime)
        ->where('check_out_time', '>', $nowTime)
        ->whereHas('room', function (Builder $query) use ($id) {
            $query->where('room_no', $id);
        })
        ->with(['client' => function($query){
            $query->with('isTgor', 'salutation', 'religion', 'preferredContactBy');
        }])
        ->with(['room' => function($query){
            $query->with('status');
        }])
        ->with(
            // 'booking_discount',
            'funeral_director',
            'room_type',
            'co_license',
            'status',
            'event'
        )->first();
        return view('room-info', compact('booking_room'));
    }
    public function getAllRoomBooking(Request $request){
        $nowDate = Carbon::now()->toDateString();
        $nowTime = Carbon::now()->toTimeString();
        $booking_room = BookingLineItems::where('check_in_date', '<=', $nowDate)
        ->where('check_out_date', '>=', $nowDate)
        ->where('check_in_time', '<', $nowTime)
        ->where('check_out_time', '>', $nowTime)
        ->with(['client' => function($query){
            $query->with('isTgor', 'salutation', 'religion', 'preferredContactBy');
        }])
        ->with(['room' => function($query){
            $query->with('status');
        }])
        ->with(
            // 'booking_discount',
            'funeral_director',
            'room_type',
            'co_license',
            'status',
            'event'
        )->get();
        return view('room', compact('booking_room'));
    }
}
