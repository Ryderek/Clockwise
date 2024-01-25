<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/admin'.env("APP_ADMIN_POSTFIX").'', [App\Http\Controllers\PageController::class, 'index'])->name('admin')->middleware('auth');

Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/ajax/{functionName}', [App\Http\Controllers\AjaxController::class, 'handler'])->name('ajax')->middleware('auth');

Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/accounting', [App\Http\Controllers\AccountingController::class, 'accountingIndex'])->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/accounting/forceStopTime', [App\Http\Controllers\AccountingController::class, 'forceStopTime'])->name("accounting.forceStopTime")->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/accounting/modifyWorktime', [App\Http\Controllers\AccountingController::class, 'modifyWorktime'])->name("accounting.modifyWorktime")->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/accounting/order/{orderId}', [App\Http\Controllers\AccountingController::class, 'accountingOrder'])->name('accounting.order')->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/accounting/{page}', [App\Http\Controllers\AccountingController::class, 'accountingIndex'])->name('accounting')->middleware('auth');

Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/attachment/create', [App\Http\Controllers\AttachmentController::class, 'store'])->name('attachment.create')->middleware('auth');

Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/cron/kickForgetfulLefters', [App\Http\Controllers\CronController::class, 'kickForgetfulLefters'])->name('cron.kickForgetfulLefters');

Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/deployment/{orderId}/generate', [App\Http\Controllers\DeploymentController::class, 'generateOutput'])->name('admin.deployment.generate')->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/deployment/{orderId?}', [App\Http\Controllers\DeploymentController::class, 'index'])->name('admin.deployment')->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/deployment/{orderId}/{detailId}', [App\Http\Controllers\DeploymentController::class, 'deployDetail'])->name('admin.deployment.deployDetail')->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/deployment/insertDeploy', [App\Http\Controllers\DeploymentController::class, 'insertDeploy'])->name('admin.deployment.insertDeploy')->middleware('auth');

Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/detail/store', [App\Http\Controllers\OrderDetailController::class, 'store'])->name('detail.store')->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/detail/update', [App\Http\Controllers\OrderDetailController::class, 'update'])->name('detail.update')->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/detail/remove/{id}', [App\Http\Controllers\OrderDetailController::class, 'softDelete'])->name("detail.remove")->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/detail/{id}', [App\Http\Controllers\OrderDetailController::class, 'edit'])->name("detail.edit")->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/docs/generate-summary/{orderId}', [App\Http\Controllers\PrintableController::class, 'orderSummary'])->name("docs.generate-summary")->middleware('auth');

Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/employee/create', [App\Http\Controllers\UserController::class, 'create'])->name("employee.create")->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/employee/store', [App\Http\Controllers\UserController::class, 'store'])->name("employee.store")->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/employee/delete', [App\Http\Controllers\UserController::class, 'remove'])->name('employee.delete')->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/employee/{id}', [App\Http\Controllers\UserController::class, 'edit'])->name("employee")->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/employee/update', [App\Http\Controllers\UserController::class, 'update'])->name('employee.update')->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/employees', [App\Http\Controllers\UserController::class, 'index'])->name("employees")->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/employees/{page}', [App\Http\Controllers\UserController::class, 'index'])->middleware('auth');

Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/gantt/data/{id}', [App\Http\Controllers\GanttController::class, 'loadData'])->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/gantt/{id}', [App\Http\Controllers\GanttController::class, 'show'])->name("gantt")->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/gantt-reasume/data/{date}', [App\Http\Controllers\GanttReasumeController::class, 'loadData'])->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/gantt-reasume/{date?}', [App\Http\Controllers\GanttReasumeController::class, 'show'])->name("gantt-reasume")->middleware('auth');

Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/group/create', [App\Http\Controllers\GroupController::class, 'create'])->name("group.create")->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/group/store', [App\Http\Controllers\GroupController::class, 'store'])->name("group.store")->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/group/delete', [App\Http\Controllers\GroupController::class, 'remove'])->name("group.delete")->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/group/{id}', [App\Http\Controllers\GroupController::class, 'edit'])->name("group")->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/group/update', [App\Http\Controllers\GroupController::class, 'update'])->name('group.update')->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/group/update-permissions', [App\Http\Controllers\GroupController::class, 'updatePermissions'])->name('group.update-permissions')->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/groups', [App\Http\Controllers\GroupController::class, 'index'])->name("groups")->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/groups/{page}', [App\Http\Controllers\GroupController::class, 'index'])->middleware('auth');

Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/identity-cards', [App\Http\Controllers\AuthCardController::class, 'index'])->name("identity-cards")->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/identity-card/create', [App\Http\Controllers\AuthCardController::class, 'store'])->name("identity-card.create")->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/identity-card/delete', [App\Http\Controllers\AuthCardController::class, 'remove'])->name('identity-card.remove')->middleware('auth');

Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/note/create/{relator}/{relatorId}', [App\Http\Controllers\NoteController::class, 'create'])->name('note.create')->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/note/create', [App\Http\Controllers\NoteController::class, 'store'])->name('note.create-new')->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/note/{id}', [App\Http\Controllers\NoteController::class, 'edit'])->name('note.edit')->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/note/update', [App\Http\Controllers\NoteController::class, 'update'])->name('note.update')->middleware('auth');

Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/notification/dismiss', [App\Http\Controllers\NotificationController::class, 'dismissNotification'])->name('notification-dismiss')->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/notification/create', [App\Http\Controllers\NotificationController::class, 'createNotification'])->name('notification-create')->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications')->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/notifications/{page}', [App\Http\Controllers\NotificationController::class, 'index'])->middleware('auth');

Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/order/push', [App\Http\Controllers\OrderController::class, 'pushStatusForward'])->name('order.push')->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/order/update', [App\Http\Controllers\OrderController::class, 'update'])->name('order.update')->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/order/live/{id}', [App\Http\Controllers\OrderController::class, 'watchLiveLog'])->name("order.live")->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/order/remove/{id}', [App\Http\Controllers\OrderController::class, 'softDelete'])->name("order.remove")->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/order/{id}', [App\Http\Controllers\OrderController::class, 'edit'])->name("order.edit")->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/orders', [App\Http\Controllers\OrderController::class, 'index'])->name("orders")->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/orders/create', [App\Http\Controllers\OrderController::class, 'create'])->name("order.create")->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/orders/create', [App\Http\Controllers\OrderController::class, 'store'])->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/orders/{id}', [App\Http\Controllers\OrderController::class, 'indexByPage'])->where('id', '[0-9]+')->name("orders.page")->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/orders/{status}', [App\Http\Controllers\OrderController::class, 'indexByStatus'])->where('status', '[A-Za-z]+-?[A-Za-z]+')->name("orders.status")->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/orders/{status}/{page}', [App\Http\Controllers\OrderController::class, 'indexByStatus'])->middleware('auth');

Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/print/{refererSlug}/{refererArgument}', [App\Http\Controllers\PrintableController::class, 'print'])->name("print")->middleware('auth');

Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/role/create', [App\Http\Controllers\RoleController::class, 'create'])->name("role.create")->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/role/create', [App\Http\Controllers\RoleController::class, 'store'])->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/role/update', [App\Http\Controllers\RoleController::class, 'update'])->name('role.update')->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/role/delete', [App\Http\Controllers\RoleController::class, 'remove'])->name('role.delete')->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/role/assign', [App\Http\Controllers\RoleController::class, 'assign'])->name('role.assign')->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/role/release', [App\Http\Controllers\RoleController::class, 'release'])->name('role.release')->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/role/{id}', [App\Http\Controllers\RoleController::class, 'edit'])->name("role.edit")->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/roles', [App\Http\Controllers\RoleController::class, 'index'])->name("roles")->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/roles/{id}', [App\Http\Controllers\RoleController::class, 'index'])->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/roles/create', [App\Http\Controllers\RoleController::class, 'store'])->middleware('auth');

Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/salaries', [App\Http\Controllers\AccountingController::class, 'salariesIndex'])->name('salaries')->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/salaries/update', [App\Http\Controllers\AccountingController::class, 'salariesUpdate'])->name('salaries-update')->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/salaries/{date?}', [App\Http\Controllers\AccountingController::class, 'salariesIndex'])->name('salaries-auth')->middleware('auth');

Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/settlement', [App\Http\Controllers\AccountingController::class, 'settlementIndex'])->name('settlement')->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/settlement/create-break', [App\Http\Controllers\AccountingController::class, 'settlementCreateBreak'])->name('settlement.create-break')->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/settlement/user/{id}', [App\Http\Controllers\AccountingController::class, 'settlementUser'])->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/settlement/{page}', [App\Http\Controllers\AccountingController::class, 'settlementIndex'])->middleware('auth');

Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/tool/create', [App\Http\Controllers\ToolController::class, 'create'])->name("tool.create")->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/tool/create', [App\Http\Controllers\ToolController::class, 'store'])->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/tool/delete', [App\Http\Controllers\ToolController::class, 'remove'])->name('tool.delete')->middleware('auth');
Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/tool/update', [App\Http\Controllers\ToolController::class, 'update'])->name('tool.update')->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/tool/{id}', [App\Http\Controllers\ToolController::class, 'edit'])->name("tool.edit")->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/tools/{status}', [App\Http\Controllers\ToolController::class, 'index'])->name("tools.status")->middleware('auth');
Route::get('/admin'.env("APP_ADMIN_POSTFIX").'/tools/{status}/{page}', [App\Http\Controllers\ToolController::class, 'index'])->middleware('auth');

Route::post('/admin'.env("APP_ADMIN_POSTFIX").'/worktiming/define-estimated', [App\Http\Controllers\WorkTimingController::class, 'defineEstimated'])->name('worktiming.define-estimated')->middleware('auth');

Route::post('/api/{apiName}/{functionName}', [App\Http\Controllers\ApiController::class, 'handleApiRequest'])->middleware('auth');

Route::get('/produkcja'.env("APP_PRODUCTION_POSTFIX"), [App\Http\Controllers\PageController::class, 'production'])->name("production")->middleware('auth.basic');
Route::post('/produkcja'.env("APP_PRODUCTION_POSTFIX"), [App\Http\Controllers\PageController::class, 'productionVerify'])->name('production.post')->middleware('auth.basic');
Route::get('/produkcja'.env("APP_PRODUCTION_POSTFIX").'/dashboard', [App\Http\Controllers\ProductionController::class, 'index'])->name("production.dashboard")->middleware('auth.basic');
Route::post('/produkcja'.env("APP_PRODUCTION_POSTFIX").'/dashboard', [App\Http\Controllers\ProductionController::class, 'dynamicDashboard'])->middleware('auth.basic');
Route::get('/produkcja'.env("APP_PRODUCTION_POSTFIX").'/detailing', [App\Http\Controllers\ProductionController::class, 'detailing'])->name("production.detailing")->middleware('auth.basic');
Route::post('/produkcja'.env("APP_PRODUCTION_POSTFIX").'/detailing', [App\Http\Controllers\ProductionController::class, 'detailingByEAN'])->name("production.detailing.ean")->middleware('auth.basic');
Route::post('/produkcja'.env("APP_PRODUCTION_POSTFIX").'/detailing/save', [App\Http\Controllers\ProductionController::class, 'detailingSave'])->name("production.detailing.save")->middleware('auth.basic');
//Route::get('/produkcja'.env("APP_PRODUCTION_POSTFIX").'/detailing/{orderId}', [App\Http\Controllers\ProductionController::class, 'detailingOrder'])->name("production.detailing.order");
//Route::get('/produkcja'.env("APP_PRODUCTION_POSTFIX").'/detailing/{orderId}/{orderDetailId}', [App\Http\Controllers\ProductionController::class, 'detailingDetail'])->name("production.detailing.detail");
Route::get('/produkcja'.env("APP_PRODUCTION_POSTFIX").'/processing', [App\Http\Controllers\ProductionController::class, 'processing'])->name("production.processing")->middleware('auth.basic');
Route::get('/produkcja'.env("APP_PRODUCTION_POSTFIX").'/processing/{workTimingId}', [App\Http\Controllers\ProductionController::class, 'processingWorkTiming'])->name("production.processing.worktime")->middleware('auth.basic');
Route::post('/produkcja'.env("APP_PRODUCTION_POSTFIX").'/processing/confirm', [App\Http\Controllers\ProductionController::class, 'processingDone'])->name("production.processing.done")->middleware('auth.basic');

Route::get('/time-tracking'.env("APP_TIMETRACKING_POSTFIX"), [App\Http\Controllers\TimeTrackingController::class, 'index'])->name("time-tracking");
Route::post('/time-tracking'.env("APP_TIMETRACKING_POSTFIX"), [App\Http\Controllers\TimeTrackingController::class, 'timeTrackingVerify'])->name('time-tracking.post');
Route::post('/time-tracking'.env("APP_TIMETRACKING_POSTFIX").'/logout', [App\Http\Controllers\TimeTrackingController::class, 'logout'])->name("time-tracking.logout");
Route::post('/time-tracking'.env("APP_TIMETRACKING_POSTFIX").'/switch-worktime', [App\Http\Controllers\TimeTrackingController::class, 'switchWorktime'])->name("time-tracking.switch-worktime");

Route::get('/uploaded/{path}', [App\Http\Controllers\PageController::class, 'uploaded'])->where('path', '.*')->middleware('auth');