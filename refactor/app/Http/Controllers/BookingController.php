<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     * @var Request
     */
    protected $bookingRepository;
    protected $request;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(Request $request, BookingRepository $bookingRepository)
    {
        $this->request = $request;
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * @param 
     * @return mixed
     */
    public function index()
    {
        if($user_id = $this->request->get('user_id')) {

            $response = $this->bookingRepository->getUsersJobs($user_id);

        }
        elseif($this->request->__authenticatedUser->user_type == env('ADMIN_ROLE_ID') || $this->request->__authenticatedUser->user_type == env('SUPERADMIN_ROLE_ID'))
        {
            $response = $this->bookingRepository->getAll($this->request);
        }

        return response($response);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $job = $this->bookingRepository->with('translatorJobRel.user')->find($id);

        return response($job);
    }

    /**
     * @return mixed
     */
    public function store()
    {
        $data = $this->request->all();

        $response = $this->bookingRepository->store($this->request->__authenticatedUser, $data);

        return response($response);

    }

    /**
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $data = $this->request->except(['_token', 'submit']);
        $cuser = $this->request->__authenticatedUser;
        $response = $this->bookingRepository->updateJob($id, $data, $cuser);

        return response($response);
    }

    /**
     * @return mixed
     */
    public function immediateJobEmail()
    {
        $data = $this->request->all();

        $response = $this->bookingRepository->storeJobEmail($data);

        return response($response);
    }

    /**
     * @return mixed
     */
    public function getHistory()
    {
        if($user_id = $this->request->get('user_id')) {

            $response = $this->bookingRepository->getUsersJobsHistory($user_id, $this->request);
            return response($response);
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function acceptJob()
    {
        $data = $this->request->all();
        $user = $this->request->__authenticatedUser;

        $response = $this->bookingRepository->acceptJob($data, $user);

        return response($response);
    }

    public function acceptJobWithId()
    {
        $data = $this->request->get('job_id');
        $user = $this->request->__authenticatedUser;

        $response = $this->bookingRepository->acceptJobWithId($data, $user);

        return response($response);
    }

    /**
     * @return mixed
     */
    public function cancelJob()
    {
        $data = $this->request->all();
        $user = $this->request->__authenticatedUser;

        $response = $this->bookingRepository->cancelJobAjax($data, $user);

        return response($response);
    }

    /**
     * @return mixed
     */
    public function endJob()
    {
        $data = $this->request->all();

        $response = $this->bookingRepository->endJob($data);

        return response($response);

    }

    public function customerNotCall()
    {
        $data = $this->request->all();

        $response = $this->bookingRepository->customerNotCall($data);

        return response($response);

    }

    /**
     * @return mixed
     */
    public function getPotentialJobs()
    {
        // unused line of code remove it.
        $data = $this->request->all();
        
        $user = $this->request->__authenticatedUser;

        $response = $this->bookingRepository->getPotentialJobs($user);

        return response($response);
    }

    public function distanceFeed()
    {
        $data = $this->request->all();

        if (isset($data['distance']) && $data['distance'] != "") {
            $distance = $data['distance'];
        } else {
            $distance = "";
        }
        if (isset($data['time']) && $data['time'] != "") {
            $time = $data['time'];
        } else {
            $time = "";
        }
        if (isset($data['jobid']) && $data['jobid'] != "") {
            $jobid = $data['jobid'];
        }

        if (isset($data['session_time']) && $data['session_time'] != "") {
            $session = $data['session_time'];
        } else {
            $session = "";
        }

        if ($data['flagged'] == 'true') {
            if($data['admincomment'] == '') return "Please, add comment";
            $flagged = 'yes';
        } else {
            $flagged = 'no';
        }
        
        if ($data['manually_handled'] == 'true') {
            $manually_handled = 'yes';
        } else {
            $manually_handled = 'no';
        }

        if ($data['by_admin'] == 'true') {
            $by_admin = 'yes';
        } else {
            $by_admin = 'no';
        }

        if (isset($data['admincomment']) && $data['admincomment'] != "") {
            $admincomment = $data['admincomment'];
        } else {
            $admincomment = "";
        }
        if ($time || $distance) {
            // no need to make variable
            $affectedRows = Distance::where('job_id', '=', $jobid)->update(array('distance' => $distance, 'time' => $time));
        }

        if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {
            // no need to make variable
            $affectedRows1 = Job::where('id', '=', $jobid)->update(array('admin_comments' => $admincomment, 'flagged' => $flagged, 'session_time' => $session, 'manually_handled' => $manually_handled, 'by_admin' => $by_admin));

        }

        return response('Record updated!');
    }

    public function reopen()
    {
        $data = $this->request->all();
        $response = $this->bookingRepository->reopen($data);

        return response($response);
    }

    public function resendNotifications()
    {
        $data = $this->request->all();
        $job = $this->bookingRepository->find($data['jobid']);
        $job_data = $this->bookingRepository->jobToData($job);
        $this->bookingRepository->sendNotificationTranslator($job, $job_data, '*');

        return response(['success' => 'Push sent']);
    }

    /**
     * Sends SMS to Translator
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications()
    {
        $data = $this->request->all();
        $job = $this->bookingRepository->find($data['jobid']);
        $job_data = $this->bookingRepository->jobToData($job);

        try {
            $this->bookingRepository->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }
    }

}