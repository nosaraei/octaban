<?php namespace System\Classes;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use System\Helpers\CoreUtils;
use Tymon\JWTAuth\Exceptions\JWTException;
use October\Rain\Exception\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use DB;

class ApiController extends \Illuminate\Routing\Controller
{
    private $user = null;

    private $isTransactionBegin = false;

    public function callAction($method, $parameters)
    {
        try{

            return call_user_func_array([$this, $method], $parameters);

        }
//        catch(CurlerException $ex){
//
//            $this->transactionRollBack();
//
//            return response()->json([
//                'success' => false,
//                'message' => $ex->user_message,
//                'dev_message' => $ex->statusCode . " " . $ex->getMessage()
//            ], 200); //502);
//
//        }
        catch (JWTException $ex) {

            $this->transactionRollBack();

            return response()->json([
                'success' => false,
                'message' => trans("backend::lang.global.messages.not_authorized"),
                'dev_message' => $ex->getMessage(),
                'results' => null
            ], 401);

        }
        catch (ValidationException $ex) {

            $this->transactionRollBack();

            return response()->json([
                'success' => false,
                'message' => $ex->getMessage(),
                'dev_message' => "Some Input Are Invalid, In 'validation' Key See Them!",
                'validation' => $ex->getErrors(),
                'results' => null
            ], 400);

        }
        catch(ModelNotFoundException $ex){

            $this->transactionRollBack();

            $model_name = $ex->getModel() ? strtolower(class_basename($ex->getModel())) : "resource";

            $id = $ex->getIds() ? " with id: " . $ex->getIds()[0] : "";

            return response()->json([
                'success' => false,
                'message' => trans("backend::lang.global.messages.not_found", ["item" => $model_name]),
                'dev_message' => "Not found $model_name$id",
            ], 404);

        }
        catch(AccessDeniedException $ex){

            $this->transactionRollBack();

            return response()->json([
                'success' => false,
                'message' => trans("backend::lang.global.messages.access_denied"),
                'dev_message' => 'You do not have access',
                'results' => null
            ], 403);

        }
        catch(\Exception $ex){

            $this->transactionRollBack();

            trace_log($ex);

            return response()->json([
                'success' => false,
                'message' => trans("backend::lang.global.messages.server_error"),
                'dev_message' =>  $ex->getMessage() . " --- file: " . $ex->getFile() . " --- line: " . $ex->getLine(),
                'results' => null
            ], 500);

        }

    }

    /**
     * @param array|null $results
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success(array $results = null, $message = ""){

        $response = [
            'success' => true,
            'message' => trans($message),
            'dev_message' => "",
            'results' => null
        ];

        if($results){
            $response["results"] = $results;
        }

        return response()->json($response);

    }

    /**
     * @param string $message
     * @param string $dev_message
     * @param array|null $results
     * @param integer|null $error_code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error($message, $dev_message = "", array $results = null){

        $response = [
            'success' => false,
            'message' => trans($message),
            'dev_message' => $dev_message,
            'results' => null
        ];

        if($results){
            $response["results"] = $results;
        }

        return response()->json($response);

    }

    protected function transactionBegin(){

        DB::beginTransaction();
        $this->isTransactionBegin = true;

    }

    protected function transactionEnd(){

        if($this->isTransactionBegin){

            DB::commit();
            $this->isTransactionBegin = false;
        }

    }

    protected function transactionRollBack(){

        if($this->isTransactionBegin){

            DB::rollBack();
            $this->isTransactionBegin = false;
        }

    }

    public function __get($name)
    {
        if($name == "user"){

            if(!$this->user){

                $this->user = JWTAuth::parseToken()->authenticate();
            }

            return $this->user;
        }
        else if($name == "user_is_login"){

            if(!$this->user){

                try {
                    $this->user = JWTAuth::parseToken()->authenticate();
                    return true;
                }
                catch (JWTException $ex){
                    return false;
                }

            }

            return true;
        }

        return null;
    }

    public function __set($name, $value)
    {
        if($name == "user"){

            $this->user = $value;

        }

    }

    protected function pagination(Request $request, $query, $field)
    {
        $page = $request->page?:1;
        $per_page = $request->per_page?:15;
        $total_count = $query->count();

        $custom_fields =  CoreUtils::collect($request->custom_fields);

        $query = $query->forPage($page, $per_page);

        return [
            $field => count($custom_fields) ? $query->get()->map->only($custom_fields->toArray()) : $query->get(),
            "pagination" => [
                "current_page" => (integer) $page,
                "last_page" => ceil($total_count / $per_page),
                "per_page" => (integer) $per_page,
                "total_count" => $total_count,
            ]
        ];
    }

    protected function paginationDirty(Request $request, $collections, $field)
    {
        $page = $request->page?:1;
        $per_page = $request->per_page?:15;
        $total_count = $collections->count();

        $custom_fields = CoreUtils::collect($request->custom_fields);

        $collections = $collections->forPage($page, $per_page);

        return [
            $field => count($custom_fields) ? $collections->map->only($custom_fields->toArray()) : $collections,
            "pagination" => [
                "current_page" => (integer) $page,
                "last_page" => ceil($total_count / $per_page),
                "per_page" => (integer) $per_page,
                "total_count" => $total_count,
            ]
        ];
    }


}
