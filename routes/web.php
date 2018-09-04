<?php


Route::group(['domain' => 'localvchip.com'], function () {
	Route::get('/', 'HomeController@home');
	Route::get('/home', 'HomeController@home');
	// chat
	Route::post('sendMessage', 'ChatController@sendMessage');
	Route::post('privateChat', 'ChatController@privateChat');
	Route::post('showChatUsers', 'ChatController@showChatUsers');
	Route::post('loadChatUsers', 'ChatController@loadChatUsers');
	Route::post('checkOnlineUsers', 'ChatController@checkOnlineUsers');
	Route::post('readChatMessages', 'ChatController@readChatMessages');
	Route::post('checkDashboardOnlineUsers', 'ChatController@checkOnlineUsers');

	// OAuth Routes
	Route::get('/auth/{provider}', 'SocialiteController@redirectToProvider');
	Route::get('/auth/{provider}/callback', 'SocialiteController@handleProviderCallback');

	// admin course category
	Route::get('admin/manageCourseCategory', 'Course\CourseCategoryController@show');
	Route::get('admin/createCourseCategory', 'Course\CourseCategoryController@create');
	Route::post('admin/createCourseCategory', 'Course\CourseCategoryController@store');
	Route::get('admin/coursecategory/{id}/edit', 'Course\CourseCategoryController@edit');
	Route::put('admin/updateCourseCategory', 'Course\CourseCategoryController@update');
	Route::delete('admin/deleteCourseCategory', 'Course\CourseCategoryController@delete');
	Route::post('admin/isCourseCategoryExist', 'Course\CourseCategoryController@isCourseCategoryExist');

	// manage course all
	Route::get('admin/manageCourseAll', 'Course\CourseAllController@showAll');
	Route::post('admin/createAllCourseCategory', 'Course\CourseAllController@storeCategory');
	Route::post('admin/createAllCourseSubCategory', 'Course\CourseAllController@storeSubCategory');
	Route::post('admin/createAllCourseCourse', 'Course\CourseAllController@storeCourse');

	// admin course sub category
	Route::get('admin/manageCourseSubCategory', 'Course\CourseSubCategoryController@show');
	Route::get('admin/createCourseSubCategory', 'Course\CourseSubCategoryController@create');
	Route::post('admin/createCourseSubCategory', 'Course\CourseSubCategoryController@store');
	Route::get('admin/coursesubcategory/{id}/edit', 'Course\CourseSubCategoryController@edit');
	Route::put('admin/updateCourseSubCategory', 'Course\CourseSubCategoryController@update');
	Route::delete('admin/deleteCourseSubCategory', 'Course\CourseSubCategoryController@delete');
	Route::post('admin/isCourseSubCategoryExist', 'Course\CourseSubCategoryController@isCourseSubCategoryExist');

	// admin course course
	Route::get('admin/manageCourseCourse', 'Course\CourseCourseController@show');
	Route::get('admin/createCourseCourse', 'Course\CourseCourseController@create');
	Route::post('admin/createCourseCourse', 'Course\CourseCourseController@store');
	Route::get('admin/courseCourse/{id}/edit', 'Course\CourseCourseController@edit');
	Route::put('admin/updateCourseCourse', 'Course\CourseCourseController@update');
	Route::delete('admin/deleteCourseCourse', 'Course\CourseCourseController@delete');
	Route::post('admin/getCourseSubCategories', 'Course\CourseCourseController@getCourseSubCategories');
	Route::post('admin/isCourseCourseExist', 'Course\CourseCourseController@isCourseCourseExist');
	Route::post('admin/getCourseByCatIdBySubCatIdForAdmin', 'Course\CourseCourseController@getCourseByCatIdBySubCatIdForAdmin');

	// admin course video
	Route::get('admin/manageCourseVideo', 'Course\CourseVideoController@show');
	Route::get('admin/createCourseVideo', 'Course\CourseVideoController@create');
	Route::post('admin/createCourseVideo', 'Course\CourseVideoController@store');
	Route::get('admin/courseVideo/{id}/edit', 'Course\CourseVideoController@edit');
	Route::put('admin/updateCourseVideo', 'Course\CourseVideoController@update');
	Route::delete('admin/deleteCourseVideo', 'Course\CourseVideoController@delete');
	Route::post('admin/isCourseVideoExist', 'Course\CourseVideoController@isCourseVideoExist');

	//Admin Login
	Route::get('admin/login', 'AdminAuth\LoginController@showLoginForm');
	Route::get('admin', 'AdminAuth\LoginController@redirectToLoginForm');
	Route::post('admin/login', 'AdminAuth\LoginController@login');
	Route::post('admin/logout', 'AdminAuth\LoginController@logout');

	// Admin
	Route::get('admin/home', 'Admin\AdminController@home');
	Route::get('admin/manageSendMails', 'Admin\AdminController@writeMail');
	Route::post('admin/sendSubscribedMails', 'Admin\AdminController@sendSubscribedMails');
	Route::get('admin/manageClients', 'Admin\AdminController@manageClients');
	Route::post('admin/changeClientPermissionStatus', 'Admin\AdminController@changeClientPermissionStatus');
	Route::delete('admin/deleteClient', 'Admin\AdminController@deleteClient');
	Route::get('admin/manageClientHistory', 'Admin\AdminController@manageClientHistory');
	Route::post('admin/getClientHistory', 'Admin\AdminController@getClientHistory');
	Route::get('admin/manageWebDevelopments', 'Admin\AdminController@manageWebDevelopments');

	// Admin all users
	Route::get('admin/allUsers', 'Admin\AllUsersInfoController@allUsers');
	Route::post('admin/showOtherStudents', 'Admin\AllUsersInfoController@showOtherStudents');
	Route::post('admin/deleteStudent', 'Admin\AllUsersInfoController@deleteStudent');
	Route::post('admin/getDepartments', 'Admin\AllUsersInfoController@getDepartments');
	Route::post('admin/changeUserApproveStatus', 'Admin\AllUsersInfoController@changeUserApproveStatus');
	Route::post('admin/searchUsers', 'Admin\AllUsersInfoController@searchUsers');
	Route::get('admin/userTestResults/{id?}', 'Admin\AllUsersInfoController@userTestResults');
	Route::post('admin/showUserTestResults', 'Admin\AllUsersInfoController@showUserTestResults');
	Route::get('admin/userCourses/{id?}', 'Admin\AllUsersInfoController@userCourses');
	Route::post('admin/showUserCourses', 'Admin\AllUsersInfoController@showUserCourses');
	Route::get('admin/userPlacement/{id?}', 'Admin\AllUsersInfoController@userPlacement');
	Route::post('admin/getStudentById', 'Admin\AllUsersInfoController@getStudentById');
	Route::get('admin/userVideo/{id?}', 'Admin\AllUsersInfoController@userVideo');
	Route::put('admin/updateStudentVideo', 'Admin\AllUsersInfoController@updateStudentVideo');
	Route::get('admin/unapproveUsers', 'Admin\AllUsersInfoController@unapproveUsers');
	Route::post('admin/unapproveUsersByCollegeId', 'Admin\AllUsersInfoController@unapproveUsersByCollegeId');
	Route::post('admin/approveUser', 'Admin\AllUsersInfoController@approveUser');
	Route::get('admin/allTestResults', 'Admin\AllUsersInfoController@allTestResults');
	Route::post('admin/getAllTestResults', 'Admin\AllUsersInfoController@getAllTestResults');
	Route::get('admin/downloadExcelResult', 'Admin\AllUsersInfoController@downloadExcelResult');

	// admin college info
	Route::get('admin/manageCollegeInfo', 'Test\CollegeInfoController@manageCollegeInfo');
	Route::get('admin/createCollege', 'Test\CollegeInfoController@create');
	Route::post('admin/createCollege', 'Test\CollegeInfoController@store');
	Route::get('admin/college/{id}/edit', 'Test\CollegeInfoController@edit');
	Route::put('admin/updateCollege', 'Test\CollegeInfoController@update');
	Route::delete('admin/deleteCollege', 'Test\CollegeInfoController@delete');
	Route::post('admin/isCollegeExist', 'Test\CollegeInfoController@isCollegeExist');

	// user login
	Route::get('login', 'UserAuth\LoginController@showLoginForm');
	Route::post('login', 'UserAuth\LoginController@login');
	Route::post('/logout', 'UserAuth\LoginController@logout');
	Route::post('userLogin', 'UserAuth\LoginController@userLogin');

	//User Register
	// Route::get('register',. 'UserAuth\RegisterController@showRegistrationForm');
	Route::post('register', 'UserAuth\RegisterController@register');
	Route::get('forgotPassword', 'UserAuth\ForgotPasswordController@showLinkRequestForm');
	Route::post('password/email', 'UserAuth\ForgotPasswordController@sendPasswordResetLink');
	Route::get('password/reset/{token}', 'UserAuth\ResetPasswordController@showResetForm');
	Route::post('password/reset', 'UserAuth\ResetPasswordController@reset');
	Route::get('register/verify/{token}', 'UserAuth\RegisterController@verify');
	Route::get('signup', 'HomeController@signup');
	Route::post('getDepartments', 'HomeController@getDepartments');
	Route::post('doAdvertisementPayment', 'HomeController@doAdvertisementPayment');
	Route::get('thankyouadvertisement', 'HomeController@thankyouadvertisement');
	Route::any('webhookAdvertisement', 'HomeController@webhookAdvertisement');

	// manage sub admin
	Route::get('admin/manageSubadminUser', 'Admin\SubadminController@show');
	Route::get('admin/createSubAdmin', 'Admin\SubadminController@create');
	Route::post('admin/createSubAdmin', 'Admin\SubadminController@store');
	Route::get('admin/subadmin/{id}/edit', 'Admin\SubadminController@edit');
	Route::put('admin/updateSubAdmin', 'Admin\SubadminController@update');

	// admin test category
	Route::get('admin/manageCategory', 'Test\CategoryController@show');
	Route::get('admin/createCategory', 'Test\CategoryController@create');
	Route::post('admin/createCategory', 'Test\CategoryController@store');
	Route::get('admin/category/{id}/edit', 'Test\CategoryController@edit');
	Route::put('admin/updateCategory', 'Test\CategoryController@update');
	Route::delete('admin/deleteCategory', 'Test\CategoryController@delete');
	Route::post('admin/isTestCategoryExist', 'Test\CategoryController@isTestCategoryExist');

	// admin question bank category
	Route::get('admin/manageQuestionBankCategory', 'QuestionBank\QuestionBankCategoryController@show');
	Route::get('admin/createQuestionBankCategory', 'QuestionBank\QuestionBankCategoryController@create');
	Route::post('admin/createQuestionBankCategory', 'QuestionBank\QuestionBankCategoryController@store');
	Route::get('admin/questionBankCategory/{id}/edit', 'QuestionBank\QuestionBankCategoryController@edit');
	Route::put('admin/updateQuestionBankCategory', 'QuestionBank\QuestionBankCategoryController@update');
	Route::delete('admin/deleteQuestionBankCategory', 'QuestionBank\QuestionBankCategoryController@delete');
	Route::post('admin/isQuestionBankCategoryExist', 'QuestionBank\QuestionBankCategoryController@isQuestionBankCategoryExist');

	// admin test All
	Route::get('admin/manageTestAll', 'Test\TestAllController@showAll');
	Route::post('admin/createAllTestCategory', 'Test\TestAllController@storeCategory');
	Route::post('admin/createAllTestSubCategory', 'Test\TestAllController@storeSubCategory');
	Route::post('admin/createAllTestSubject', 'Test\TestAllController@storeSubject');
	Route::post('admin/createAllTestPaper', 'Test\TestAllController@storePaper');

	// admin test sub category
	Route::get('admin/manageSubCategory', 'Test\SubCategoryController@show');
	Route::get('admin/createSubCategory', 'Test\SubCategoryController@create');
	Route::post('admin/createSubCategory', 'Test\SubCategoryController@store');
	Route::get('admin/subCategory/{id}/edit', 'Test\SubCategoryController@edit');
	Route::put('admin/updateSubCategory', 'Test\SubCategoryController@update');
	Route::delete('admin/deleteSubCategory', 'Test\SubCategoryController@delete');
	Route::post('admin/getSubCategories', [ 'as' => 'admin/getSubCategories', 'uses' => 'Test\SubCategoryController@getSubCategories' ]);
	Route::post('admin/isTestSubCategoryExist', 'Test\SubCategoryController@isTestSubCategoryExist');

	// admin question bank sub category
	Route::get('admin/manageQuestionBankSubCategory', 'QuestionBank\QuestionBankSubCategoryController@show');
	Route::get('admin/createQuestionBankSubCategory', 'QuestionBank\QuestionBankSubCategoryController@create');
	Route::post('admin/createQuestionBankSubCategory', 'QuestionBank\QuestionBankSubCategoryController@store');
	Route::get('admin/questionBankSubCategory/{id}/edit', 'QuestionBank\QuestionBankSubCategoryController@edit');
	Route::put('admin/updateQuestionBankSubCategory', 'QuestionBank\QuestionBankSubCategoryController@update');
	Route::delete('admin/deleteQuestionBankSubCategory', 'QuestionBank\QuestionBankSubCategoryController@delete');
	Route::post('admin/isQuestionBankSubCategoryExist', 'QuestionBank\QuestionBankSubCategoryController@isQuestionBankSubCategoryExist');
	Route::post('admin/getQuestionBankSubCategories', 'QuestionBank\QuestionBankSubCategoryController@getQuestionBankSubCategories');


	// admin payable test sub category
	Route::get('admin/managePayableSubCategory', 'PayableTest\PayableSubCategoryController@show');
	Route::get('admin/createPayableSubCategory', 'PayableTest\PayableSubCategoryController@create');
	Route::post('admin/createPayableSubCategory', 'PayableTest\PayableSubCategoryController@store');
	Route::get('admin/payableSubCategory/{id}/edit', 'PayableTest\PayableSubCategoryController@edit');
	Route::put('admin/updatePayableSubCategory', 'PayableTest\PayableSubCategoryController@update');
	Route::delete('admin/deletePayableSubCategory', 'PayableTest\PayableSubCategoryController@delete');
	Route::post('admin/isPayableTestSubCategoryExist', 'PayableTest\PayableSubCategoryController@isPayableTestSubCategoryExist');

	// admin test subject
	Route::get('admin/manageSubject', 'Test\SubjectController@show');
	Route::get('admin/createSubject', 'Test\SubjectController@create');
	Route::post('admin/createSubject', 'Test\SubjectController@store');
	Route::get('admin/subject/{id}/edit', 'Test\SubjectController@edit');
	Route::put('admin/updateSubject', 'Test\SubjectController@update');
	Route::delete('admin/deleteSubject', 'Test\SubjectController@delete');
	Route::post('admin/isTestSubjectExist', 'Test\SubjectController@isTestSubjectExist');

	// admin payable subject
	Route::get('admin/managePayableSubject', 'PayableTest\PayableSubjectController@show');
	Route::get('admin/createPayableSubject', 'PayableTest\PayableSubjectController@create');
	Route::post('admin/createPayableSubject', 'PayableTest\PayableSubjectController@store');
	Route::get('admin/payableSubject/{id}/edit', 'PayableTest\PayableSubjectController@edit');
	Route::put('admin/updatePayableSubject', 'PayableTest\PayableSubjectController@update');
	Route::delete('admin/deletePayableSubject', 'PayableTest\PayableSubjectController@delete');
	Route::post('admin/isPayableSubjectExist', 'PayableTest\PayableSubjectController@isPayableSubjectExist');
	Route::post('admin/getPayableSubjectsBySubcatId', 'PayableTest\PayableSubjectController@getPayableSubjectsBySubcatId');

	// admin  test paper
	Route::get('admin/managePaper', 'Test\PaperController@show');
	Route::get('admin/createPaper', 'Test\PaperController@create');
	Route::post('admin/createPaper', 'Test\PaperController@store');
	Route::get('admin/paper/{id}/edit', 'Test\PaperController@edit');
	Route::put('admin/updatePaper', 'Test\PaperController@update');
	Route::delete('admin/deletePaper', 'Test\PaperController@delete');
	Route::post('admin/getSubjectsByCatIdBySubcatId', [ 'as' => 'admin/getSubjectsByCatIdBySubcatId','uses' => 'Test\PaperController@getSubjectsByCatIdBySubcatId' ]);
	Route::post('admin/getPaperSectionsByPaperId', [ 'as' => 'admin/getPaperSectionsByPaperId','uses' => 'Test\PaperController@getPaperSectionsByPaperId' ]);
	Route::post('admin/isTestPaperExist', 'Test\PaperController@isTestPaperExist');

	// admin payable paper
	Route::get('admin/managePayablePaper', 'PayableTest\PayablePaperController@show');
	Route::get('admin/createPayablePaper', 'PayableTest\PayablePaperController@create');
	Route::post('admin/createPayablePaper', 'PayableTest\PayablePaperController@store');
	Route::get('admin/payablePaper/{id}/edit', 'PayableTest\PayablePaperController@edit');
	Route::put('admin/updatePayablePaper', 'PayableTest\PayablePaperController@update');
	Route::delete('admin/deletePayablePaper', 'PayableTest\PayablePaperController@delete');
	Route::post('admin/isPayablePaperExist', 'PayableTest\PayablePaperController@isPayablePaperExist');
	Route::post('admin/getPayablePapersBySubjectId', 'PayableTest\PayablePaperController@getPayablePapersBySubjectId');
	Route::post('admin/getPayablePaperSectionsByPaperId', 'PayableTest\PayablePaperController@getPayablePaperSectionsByPaperId');

	// admin  test questions.
	Route::get('admin/manageQuestions', 'Test\QuestionController@index');
	Route::post('admin/showQuestions', 'Test\QuestionController@show');
	Route::get('admin/createQuestion', 'Test\QuestionController@create');
	Route::post('admin/createQuestion', 'Test\QuestionController@store');
	Route::get('admin/question/{id}/edit', 'Test\QuestionController@edit');
	Route::put('admin/updateQuestion', 'Test\QuestionController@update');
	Route::delete('admin/deleteQuestion', 'Test\QuestionController@delete');
	Route::post('admin/getPapersBySubjectId', [ 'as' => 'admin/getPapersBySubjectId','uses' => 'Test\QuestionController@getPapersBySubjectId' ]);
	Route::post('admin/getNextQuestionCount', [ 'as' => 'admin/getNextQuestionCount','uses' => 'Test\QuestionController@getNextQuestionCount' ]);
	Route::post('admin/getCurrentQuestionCount', [ 'as' => 'admin/getCurrentQuestionCount','uses' => 'Test\QuestionController@getCurrentQuestionCount' ]);
	Route::post('admin/getPrevQuestion', [ 'as' => 'admin/getPrevQuestion','uses' => 'Test\QuestionController@getPrevQuestion' ]);
	Route::get('admin/uploadQuestions', 'Test\QuestionController@uploadQuestions');
	Route::post('admin/uploadQuestions', 'Test\QuestionController@importQuestions');
	Route::get('admin/associateSession', 'Test\QuestionController@showSession');
	Route::post('admin/associateSession', 'Test\QuestionController@associateSession');
	Route::post('admin/updateQuestionSession', 'Test\QuestionController@updateQuestionSession');
	Route::post('admin/uploadTestImages', 'Test\QuestionController@uploadTestImages');
	Route::get('admin/showQuestionBank', 'Test\QuestionController@showQuestionBank');
	Route::post('admin/useQuestionBank', 'Test\QuestionController@useQuestionBank');
	Route::post('admin/exportQuestionBank', 'Test\QuestionController@exportQuestionBank');


		// admin question bank questions.
	Route::get('admin/manageQuestionBankQuestions', 'QuestionBank\QuestionBankQuestionController@index');
	Route::post('admin/showQuestionBankQuestions', 'QuestionBank\QuestionBankQuestionController@show');
	Route::get('admin/createQuestionBankQuestion', 'QuestionBank\QuestionBankQuestionController@create');
	Route::post('admin/createQuestionBankQuestion', 'QuestionBank\QuestionBankQuestionController@store');
	Route::get('admin/questionBankQuestion/{id}/edit', 'QuestionBank\QuestionBankQuestionController@edit');
	Route::put('admin/updateQuestionBankQuestion', 'QuestionBank\QuestionBankQuestionController@update');
	Route::delete('admin/deleteQuestionBankQuestion', 'QuestionBank\QuestionBankQuestionController@delete');
	Route::post('admin/getCurrentQuestionBankQuestionCount', [ 'as' => 'admin/getCurrentQuestionBankQuestionCount','uses' => 'QuestionBank\QuestionBankQuestionController@getCurrentQuestionBankQuestionCount' ]);
	Route::post('admin/getNextQuestionBankQuestionCount', [ 'as' => 'admin/getNextQuestionBankQuestionCount','uses' => 'QuestionBank\QuestionBankQuestionController@getNextQuestionBankQuestionCount' ]);
	Route::get('admin/uploadQuestionBankQuestions', 'QuestionBank\QuestionBankQuestionController@uploadQuestions');
	Route::post('admin/uploadQuestionBankQuestions', 'QuestionBank\QuestionBankQuestionController@importQuestions');
	Route::post('admin/uploadQuestionBankImages', 'QuestionBank\QuestionBankQuestionController@uploadQuestionBankImages');

	// admin payable question
	Route::get('admin/managePayableQuestions', 'PayableTest\PayableQuestionController@index');
	Route::post('admin/showPayableQuestions', 'PayableTest\PayableQuestionController@show');
	Route::get('admin/createPayableQuestion', 'PayableTest\PayableQuestionController@create');
	Route::post('admin/createPayableQuestion', 'PayableTest\PayableQuestionController@store');
	Route::get('admin/payableQuestion/{id}/edit', 'PayableTest\PayableQuestionController@edit');
	Route::put('admin/updatePayableQuestion', 'PayableTest\PayableQuestionController@update');
	Route::delete('admin/deletePayableQuestion', 'PayableTest\PayableQuestionController@delete');
	Route::post('admin/getPayableNextQuestionCount', [ 'as' => 'admin/getPayableNextQuestionCount','uses' => 'PayableTest\PayableQuestionController@getPayableNextQuestionCount' ]);
	Route::post('admin/getPayableCurrentQuestionCount', [ 'as' => 'admin/getPayableCurrentQuestionCount','uses' => 'PayableTest\PayableQuestionController@getPayableCurrentQuestionCount' ]);
	Route::post('admin/getPayablePrevQuestionCount', [ 'as' => 'admin/getPayablePrevQuestionCount','uses' => 'PayableTest\PayableQuestionController@getPayablePrevQuestionCount' ]);
	Route::get('admin/uploadPayableQuestions', 'PayableTest\PayableQuestionController@uploadPayableQuestions');
	Route::post('admin/uploadPayableQuestions', 'PayableTest\PayableQuestionController@importPayableQuestions');
	Route::post('admin/uploadPayableImages', 'PayableTest\PayableQuestionController@uploadPayableImages');


	// verify account
	Route::get('verifyAccount', 'HomeController@verifyAccount');
	Route::post('verifyEmail', 'HomeController@verifyEmail');
	// Route::post('subscribedUser', 'HomeController@subscribedUser');
	Route::get('register/verifySubscriedUser/{token}', 'HomeController@verifySubscriedUser');

	// home
	Route::get('webinar', 'HomeController@webinar');
	Route::get('webinarerror', 'HomeController@webinarerror');
	Route::get('vEducation', 'HomeController@vEducation');
	Route::get('vConnect', 'HomeController@vConnect');
	Route::get('vPendrive', 'HomeController@vPendrive');
	Route::get('vCloud', 'HomeController@vCloud');
	Route::get('liveVideo', 'HomeController@liveVideo');
	Route::get('career', 'HomeController@career');
	Route::get('heros/{id?}', 'HomeController@heros');
	Route::get('ourpartner', 'HomeController@ourpartner');
	Route::get('contactus', 'HomeController@contactus');
	Route::post('sendMail', 'HomeController@sendMail');
	Route::post('sendContactUsMail', 'HomeController@sendContactUsMail');
	Route::post('getHerosBySearchArray', 'HomeController@getHerosBySearchArray');
	Route::post('getAreasByDesignation', 'HomeController@getAreasByDesignation');
	Route::post('getHeroByDesignationByArea', 'HomeController@getHeroByDesignationByArea');
	Route::get('erp', 'HomeController@erp');
	Route::get('us', 'HomeController@us');
	Route::get('virtualplacementdrive', 'HomeController@virtualplacementdrive');
	Route::post('virtualplacementquery', 'HomeController@virtualplacementquery');
	Route::get('createAd/{page?}', 'HomeController@createAd');
	Route::post('checkStartDate', 'HomeController@checkStartDate');
	Route::post('checkDateSlot', 'HomeController@checkDateSlot');
	Route::post('showAddCalendar', 'HomeController@showAddCalendar');

	// online courses front
	Route::get('courses', 'CourseController@courses');
	Route::post('getCourseByCatIdBySubCatId', 'CourseController@getCourseByCatIdBySubCatId');
	Route::post('getCourseSubCategories', 'CourseController@getCourseSubCategories');
	Route::get('courseDetails/{id}', 'CourseController@courseDetails');
	Route::post('registerCourse', 'CourseController@courseRegister');
	Route::get('episode/{id}/{subcomment?}', [ 'as' => 'episode', 'uses' => 'CourseController@episode' ]);
	Route::post('getOnlineCourseBySearchArray', 'CourseController@getOnlineCourseBySearchArray');
	Route::post('createCourseComment', 'CourseController@createCourseComment');
	Route::post('updateCourseComment', 'CourseController@updateCourseComment');
	Route::post('deleteCourseComment', 'CourseController@deleteCourseComment');
	Route::post('createCourseSubComment', 'CourseController@createCourseSubComment');
	Route::post('updateCourseSubComment', 'CourseController@updateCourseSubComment');
	Route::post('deleteCourseSubComment', 'CourseController@deleteCourseSubComment');
	Route::post('likeCourseVideo', 'CourseController@likeCourseVideo');
	Route::post('likeCourseVideoComment', 'CourseController@likeCourseVideoComment');
	Route::post('likeCourseVideoSubComment', 'CourseController@likeCourseVideoSubComment');

	/// user Post Comment
	Route::post('createAllPost',  'PostCommentController@createAllPost');
	Route::post('createAllPostComment',  'PostCommentController@createAllPostComment');
	Route::post('createAllSubComment',  'PostCommentController@createAllSubComment');
	Route::put('updateAllPost',  'PostCommentController@updateAllPost');
	Route::delete('deleteAllPost',  'PostCommentController@deleteAllPost');
	Route::put('updateAllComment',  'PostCommentController@updateAllComment');
	Route::delete('deleteAllComment',  'PostCommentController@deleteAllComment');
	Route::put('updateAllSubComment',  'PostCommentController@updateAllSubComment');
	Route::delete('deleteAllSubComment',  'PostCommentController@deleteAllSubComment');

	//Discussion category
	Route::get('admin/manageDiscussionCategory', 'Discussion\DiscussionCategoryController@show');
	Route::get('admin/createDiscussionCategory', 'Discussion\DiscussionCategoryController@create');
	Route::post('admin/createDiscussionCategory', 'Discussion\DiscussionCategoryController@store');
	Route::get('admin/discussionCategory/{id}/edit', 'Discussion\DiscussionCategoryController@edit');
	Route::put('admin/updateDiscussionCategory', 'Discussion\DiscussionCategoryController@update');
	Route::delete('admin/deleteDiscussionCategory', 'Discussion\DiscussionCategoryController@delete');
	Route::post('admin/isDiscussionCategoryExist', 'Discussion\DiscussionCategoryController@isDiscussionCategoryExist');

	// admin  vkit category
	Route::get('admin/manageVkitCategory', 'Vkit\VkitCategoryController@show');
	Route::get('admin/createVkitCategory', 'Vkit\VkitCategoryController@create');
	Route::post('admin/createVkitCategory', 'Vkit\VkitCategoryController@store');
	Route::get('admin/vkitCategory/{id}/edit', 'Vkit\VkitCategoryController@edit');
	Route::put('admin/updateVkitCategory', 'Vkit\VkitCategoryController@update');
	Route::delete('admin/deleteVkitCategory', 'Vkit\VkitCategoryController@delete');
	Route::post('admin/isVkitCategoryExist', 'Vkit\VkitCategoryController@isVkitCategoryExist');

	// admin vkit project
	Route::get('admin/manageVkitProject', 'Vkit\VkitProjectController@show');
	Route::get('admin/createVkitProject', 'Vkit\VkitProjectController@create');
	Route::post('admin/createVkitProject', 'Vkit\VkitProjectController@store');
	Route::get('admin/vkitProject/{id}/edit', 'Vkit\VkitProjectController@edit');
	Route::put('admin/updateVkitProject', 'Vkit\VkitProjectController@update');
	Route::delete('admin/deleteVkitProject','Vkit\VkitProjectController@delete');
	Route::post('admin/isVkitProjectExist', 'Vkit\VkitProjectController@isVkitProjectExist');

	// vkits user
	Route::get('vkits', 'VkitController@show');
	Route::get('vkitproject/{id}/{subcommentId?}', [  'as' => 'vkitproject','uses' => 'VkitController@vkitproject']);
	Route::post('getVkitProjectsByCategoryId', 'VkitController@getVkitProjectsByCategoryId');
	Route::post('getVkitProjectsBySearchArray', 'VkitController@getVkitProjectsBySearchArray');
	Route::post('registerProject', 'VkitController@registerProject');
	Route::post('createProjectComment', 'VkitController@createProjectComment');
	Route::post('deleteVkitProjectComment', 'VkitController@deleteVkitProjectComment');
	Route::post('updateVkitProjectComment', 'VkitController@updateVkitProjectComment');
	Route::post('createVkitProjectSubComment', 'VkitController@createVkitProjectSubComment');
	Route::post('updateVkitProjectSubComment', 'VkitController@updateVkitProjectSubComment');
	Route::post('deleteVkitProjectSubComment', 'VkitController@deleteVkitProjectSubComment');
	Route::post('likeVkitProject', 'VkitController@likeVkitProject');
	Route::post('likeVkitProjectComment', 'VkitController@likeVkitProjectComment');
	Route::post('likekitProjectSubComment', 'VkitController@likekitProjectSubComment');

	// blog
	Route::get('blog', 'BlogController@show');
	Route::get('blogComment/{id}/{subcomment?}', [ 'as' => 'blogComment', 'uses' => 'BlogController@blogComment']);
	Route::post('createBlogComment', 'BlogController@createBlogComment');
	Route::post('createBlogSubComment', 'BlogController@createBlogSubComment');
	Route::post('getBlogsByCategoryId', 'BlogController@getBlogsByCategoryId');
	Route::get('getBlogsByCategoryId', 'BlogController@getBlogsByCategoryId');
	Route::post('updateBlogComment', 'BlogController@updateBlogComment');
	Route::post('deleteBlogComment', 'BlogController@deleteBlogComment');
	Route::post('updateBlogSubComment', 'BlogController@updateBlogSubComment');
	Route::post('deleteBlogSubComment', 'BlogController@deleteBlogSubComment');
	Route::get('tagBlogs/{id}', 'BlogController@tagBlogs');


	// admin blog category
	Route::get('admin/manageBlogCategory', 'Blog\AdminBlogCategoryController@show');
	Route::get('admin/createBlogCategory', 'Blog\AdminBlogCategoryController@create');
	Route::post('admin/createBlogCategory', 'Blog\AdminBlogCategoryController@store');
	Route::get('admin/blogCategory/{id}/edit', 'Blog\AdminBlogCategoryController@edit');
	Route::put('admin/updateBlogCategory', 'Blog\AdminBlogCategoryController@update');
	Route::delete('admin/deleteBlogCategory', 'Blog\AdminBlogCategoryController@delete');
	Route::post('admin/isBlogCategoryExist', 'Blog\AdminBlogCategoryController@isBlogCategoryExist');

	// admin blog
	Route::get('admin/manageBlog', 'Blog\AdminBlogController@show');
	Route::get('admin/createBlog', 'Blog\AdminBlogController@create');
	Route::post('admin/createBlog', 'Blog\AdminBlogController@store');
	Route::get('admin/blog/{id}/edit', 'Blog\AdminBlogController@edit');
	Route::put('admin/updateBlog', 'Blog\AdminBlogController@update');
	Route::delete('admin/deleteBlog', 'Blog\AdminBlogController@delete');
	Route::post('admin/isBlogExist', 'Blog\AdminBlogController@isBlogExist');


	// admin live courses
	Route::get('admin/manageLiveCourse', 'LiveCourse\LiveCourseController@show');
	Route::get('admin/createLiveCourse', 'LiveCourse\LiveCourseController@create');
	Route::post('admin/createLiveCourse', 'LiveCourse\LiveCourseController@store');
	Route::get('admin/liveCourse/{id}/edit', 'LiveCourse\LiveCourseController@edit');
	Route::put('admin/updateLiveCourse', 'LiveCourse\LiveCourseController@update');
	Route::delete('admin/deleteLiveCourses', 'LiveCourse\LiveCourseController@delete');
	Route::post('admin/isLiveCourseExist', 'LiveCourse\LiveCourseController@isLiveCourseExist');


	// admin live videos
	Route::get('admin/manageLiveVideo', 'LiveCourse\LiveVideoController@show');
	Route::get('admin/createLiveVideo', 'LiveCourse\LiveVideoController@create');
	Route::post('admin/createLiveVideo', 'LiveCourse\LiveVideoController@store');
	Route::get('admin/liveVideo/{id}/edit', 'LiveCourse\LiveVideoController@edit');
	Route::put('admin/updateLiveVideo', 'LiveCourse\LiveVideoController@update');
	Route::delete('admin/deleteLiveVideo', 'LiveCourse\LiveVideoController@delete');
	Route::post('admin/isLiveCourseVideoExist', 'LiveCourse\LiveVideoController@isLiveCourseVideoExist');


	// admin Documents Category
	Route::get('admin/manageDocumentsCategory', 'Documents\DocumentsCategoryController@show');
	Route::get('admin/createDocumentsCategory', 'Documents\DocumentsCategoryController@create');
	Route::post('admin/createDocumentsCategory', 'Documents\DocumentsCategoryController@store');
	Route::get('admin/documentCategory/{id}/edit', 'Documents\DocumentsCategoryController@edit');
	Route::put('admin/updateDocumentsCategory', 'Documents\DocumentsCategoryController@update');
	Route::delete('admin/deleteDocumentsCategory', 'Documents\DocumentsCategoryController@delete');
	Route::post('admin/isDocumentCategoryExist', 'Documents\DocumentsCategoryController@isDocumentCategoryExist');

	// admin Documents Docs
	Route::get('admin/manageDocumentsDoc', 'Documents\DocumentsDocController@show');
	Route::get('admin/createDocumentsDoc', 'Documents\DocumentsDocController@create');
	Route::post('admin/createDocumentsDoc', 'Documents\DocumentsDocController@store');
	Route::get('admin/documentDoc/{id}/edit', 'Documents\DocumentsDocController@edit');
	Route::put('admin/updateDocumentsDoc', 'Documents\DocumentsDocController@update');
	Route::delete('admin/deleteDocumentsDoc', 'Documents\DocumentsDocController@delete');
	Route::post('admin/isDocumentDocExist', 'Documents\DocumentsDocController@isDocumentDocExist');

	// Documents Docs user front
	Route::get('documents/{id?}', 'DocumentsController@show');
	Route::post('getDocumentsByCategoryId', 'DocumentsController@getDocumentsByCategoryId');
	Route::post('getDocumentsBySearchArray', 'DocumentsController@getDocumentsBySearchArray');
	Route::post('registerDocuments', 'DocumentsController@registerDocuments');
	Route::post('registerFavouriteDocuments', 'DocumentsController@registerFavouriteDocuments');

	// discussion and comments front
	Route::get('discussion/{commentId?}/{subcommentId?}', 'DiscussionController@discussion');
	Route::post('createPost', 'DiscussionController@createPost');
	Route::post('createComment', 'DiscussionController@createComment');
	Route::post('createSubComment', 'DiscussionController@createSubComment');
	Route::post('getDiscussionPostsByCategoryId', 'DiscussionController@getDiscussionPostsByCategoryId');
	Route::post('getDuscussionPostsBySearchArray', 'DiscussionController@getDuscussionPostsBySearchArray');
	Route::post('deleteSubComment', 'DiscussionController@deleteSubComment');
	Route::post('deleteComment', 'DiscussionController@deleteComment');
	Route::post('deletePost', 'DiscussionController@deletePost');
	Route::post('updatePost', 'DiscussionController@updatePost');
	Route::post('updateComment', 'DiscussionController@updateComment');
	Route::post('updateSubComment', 'DiscussionController@updateSubComment');
	Route::post('goToPost', 'DiscussionController@goToPost');
	Route::post('goToComment', 'DiscussionController@goToComment');


	// live course front
	Route::get('liveCourse', 'LiveCourseVideoController@show');
	Route::get('liveCourse/{id}', 'LiveCourseVideoController@showLiveCourse');
	Route::get('liveEpisode/{id}/{subcomment?}', [ 'as' => 'liveEpisode', 'uses' => 'LiveCourseVideoController@showLiveEpisode']);
	Route::post('getLiveCourseByCatId', 'LiveCourseVideoController@getLiveCourseByCatId');
	Route::post('getLiveCourseBySearchArray', 'LiveCourseVideoController@getLiveCourseBySearchArray');
	Route::get('saveTimeSecurity', 'LiveCourseVideoController@saveTimeSecurity');
	Route::post('registerLiveCourse', 'LiveCourseVideoController@registerLiveCourse');
	Route::post('createLiveCourseComment', 'LiveCourseVideoController@createLiveCourseComment');
	Route::post('updateLiveCourseComment', 'LiveCourseVideoController@updateLiveCourseComment');
	Route::post('deleteLiveCourseComment', 'LiveCourseVideoController@deleteLiveCourseComment');
	Route::post('createLiveCourseSubComment', 'LiveCourseVideoController@createLiveCourseSubComment');
	Route::post('updateLiveCourseSubComment', 'LiveCourseVideoController@updateLiveCourseSubComment');
	Route::post('deleteLiveCourseSubComment', 'LiveCourseVideoController@deleteLiveCourseSubComment');
	Route::post('likeLiveVideo', 'LiveCourseVideoController@likeLiveVideo');
	Route::post('likeLiveVideoComment', 'LiveCourseVideoController@likeLiveVideoComment');
	Route::post('likeLiveVideoSubComment', 'LiveCourseVideoController@likeLiveVideoSubComment');

	// test front
	Route::get('/instructions', 'TestController@showInstructions');
	Route::get('online-tests', 'TestController@index');
	Route::get('showTest/{id}', 'TestController@showTest');
	Route::get('getTest/{id}/{subject?}/{paper?}', 'TestController@getTest');
	Route::post('showTests', 'TestController@showTests');
	Route::post('getSubCategories', [ 'as' => 'getSubCategories', 'uses' => 'TestController@getSubCategories' ]);
	Route::post('getDataByCatSubCat', [ 'as' => 'getDataByCatSubCat', 'uses' => 'TestController@getDataByCatSubCat' ]);
	Route::post('setSessions', 'TestController@setSessions');
	Route::post('registerPaper', 'TestController@registerPaper');
	Route::post('showUserTestResult', 'TestController@showUserTestResult');
	Route::post('isTestGiven', 'TestController@isTestGiven');
	Route::post('checkVerificationCode', 'TestController@checkVerificationCode');


	// test Quiz front
	Route::post('/start-quiz', 'QuizController@startQuiz');
	Route::post('/questions', 'QuizController@getQuestions');
	Route::post('/getQuestions', 'QuizController@getAllQuestions');
	Route::post('/quiz-result', 'QuizController@getResult');
	Route::post('/solutions', 'QuizController@getSolutions');
	Route::get('/quiz-results', 'QuizController@getAllResults');
	Route::get('/user-results', 'QuizController@getUserResults');
	Route::post('showUserTestSolution', 'QuizController@showUserTestSolution');
	Route::get('downloadQuestions/{category}/{subcategory}/{subject}/{paper}', 'QuizController@downloadQuestions');

	// Account and Dashboard front
	Route::get('/account', 'AccountController@index');
	Route::get('/checkEmail', 'AccountController@checkEmail');
	Route::put('updatePassword', 'AccountController@updatePassword');
	Route::get('/profile', 'AccountController@showProfile');
	Route::get('myCourses', 'AccountController@myCourses');
	Route::get('myTest', 'AccountController@myTest');
	Route::get('myLiveCourses', 'AccountController@myLiveCourses');
	Route::get('myDocuments', 'AccountController@myDocuments');
	Route::get('myVkits', 'AccountController@myVkits');
	Route::get('myQuestions', [ 'as' => 'myQuestions', 'uses' => 'AccountController@myQuestions']);
	Route::get('myReplies', [ 'as' => 'myReplies', 'uses' => 'AccountController@myReplies']);
	Route::get('myCertificate', 'AccountController@myCertificate');
	Route::get('myFavouriteArticles', 'AccountController@myFavouriteArticles');
	Route::post('getFavouriteDocumentsByCategoryId', 'DocumentsController@getFavouriteDocumentsByCategoryId');
	Route::get('students', 'AccountController@students');
	Route::post('changeApproveStatus', 'AccountController@changeApproveStatus');
	Route::delete('deleteStudentFromCollege', 'AccountController@deleteStudentFromCollege');
	Route::post('searchStudent', 'AccountController@searchStudent');
	Route::get('studentTestResults/{id?}', 'AccountController@studentTestResults');
	Route::post('showTestResults', 'AccountController@showTestResults');
	Route::get('studentPlacement/{id?}', 'AccountController@studentPlacement');
	Route::get('studentCourses/{id?}', 'AccountController@studentCourses');
	Route::put('updateProfile', 'AccountController@updateProfile');
	Route::post('showStudentsByDepartmentByYear', 'AccountController@showStudentsByDepartmentByYear');
	Route::post('getStudentById', 'AccountController@getStudentById');
	Route::post('showStudentCourses', 'AccountController@showStudentCourses');
	Route::get('myCourseResults', 'AccountController@myCourseResults');
	Route::get('myTestResults', 'AccountController@myTestResults');
	Route::post('showUserTestResultsByCatBySubCat', 'AccountController@showUserTestResultsByCatBySubCat');
	Route::get('allTestResults', 'AccountController@allTestResults');
	Route::post('getSubjectsByCatIdBySubcatId', 'AccountController@getSubjectsByCatIdBySubcatId');
	Route::post('getPapersBySubjectId', 'AccountController@getPapersBySubjectId');
	Route::post('getAllTestResults', 'AccountController@getAllTestResults');
	Route::get('myAssignments', 'AccountController@myAssignments');
	Route::get('doAssignment/{id}', 'AccountController@doAssignment');
	Route::post('createAssignmentAnswer', 'AccountController@createAssignmentAnswer');
	Route::get('studentsAssignment', 'AccountController@studentsAssignment');
	Route::get('assignmentRemark/{assignmentId}/{studentId}', 'AccountController@assignmentRemark');
	Route::post('getDepartmentLecturers', 'AccountController@getDepartmentLecturers');
	Route::get('studentVideo/{id?}', 'AccountController@studentVideo');
	Route::put('updateStudentVideo', 'AccountController@updateStudentVideo');
	Route::post('searchContact', 'AccountController@searchContact');
	Route::post('getContacts', 'AccountController@getContacts');

	// like- dis-like count front
	Route::post('likePost', 'CourseController@likePost');
	Route::post('likeComment', 'CourseController@likeComment');
	Route::post('likeSubComment', 'CourseController@likeSubComment');
	Route::post('discussionLikePost', 'DiscussionController@discussionLikePost');
	Route::post('discussionLikeComment', 'DiscussionController@discussionLikeComment');
	Route::post('likeBlogComment', 'BlogController@likeBlogComment');
	Route::post('likeBlogSubComment', 'BlogController@likeBlogSubComment');
	Route::post('discussionLikeSubComment', 'DiscussionController@discussionLikeSubComment');
	Route::post('likeBlog', 'BlogController@likeBlog');

	// manage Designation
	Route::get('admin/manageDesignation', 'ZeroToHero\DesignationController@show');
	Route::get('admin/createDesignation', 'ZeroToHero\DesignationController@create');
	Route::post('admin/createDesignation', 'ZeroToHero\DesignationController@store');
	Route::get('admin/designation/{id}/edit', 'ZeroToHero\DesignationController@edit');
	Route::put('admin/updateDesignation', 'ZeroToHero\DesignationController@update');
	Route::delete('admin/deleteDesignation', 'ZeroToHero\DesignationController@delete');
	Route::post('admin/isDesignationExist', 'ZeroToHero\DesignationController@isDesignationExist');

	//manage Area
	Route::get('admin/manageArea', 'ZeroToHero\AreaController@show');
	Route::get('admin/createArea', 'ZeroToHero\AreaController@create');
	Route::post('admin/createArea', 'ZeroToHero\AreaController@store');
	Route::get('admin/area/{id}/edit', 'ZeroToHero\AreaController@edit');
	Route::put('admin/updateArea', 'ZeroToHero\AreaController@update');
	Route::delete('admin/deleteArea', 'ZeroToHero\AreaController@delete');
	Route::post('admin/getAreasByDesignation', 'ZeroToHero\AreaController@getAreasByDesignation');
	Route::post('admin/isAreaExist', 'ZeroToHero\AreaController@isAreaExist');

	//manage ZeroToHero
	Route::get('admin/manageZeroToHero', 'ZeroToHero\ZeroToHeroController@show');
	Route::get('admin/createZeroToHero', 'ZeroToHero\ZeroToHeroController@create');
	Route::post('admin/createZeroToHero', 'ZeroToHero\ZeroToHeroController@store');
	Route::get('admin/herotozero/{id}/edit', 'ZeroToHero\ZeroToHeroController@edit');
	Route::put('admin/updateZeroToHero', 'ZeroToHero\ZeroToHeroController@update');
	Route::delete('admin/deleteHero', 'ZeroToHero\ZeroToHeroController@delete');
	Route::post('admin/isHeroExist', 'ZeroToHero\ZeroToHeroController@isHeroExist');

	// Notifications
	Route::get('myNotifications', 'AccountController@notifications');
	Route::get('adminMessages/{year?}/{month?}', 'AccountController@adminMessages');
	Route::get('downloadExcelResult', 'AccountController@downloadExcelResult');
	Route::get('allChatMessages', 'AccountController@allChatMessages');
	Route::post('dashboardPrivateChat', 'AccountController@dashboardPrivateChat');
	Route::post('dashboardSendMessage', 'AccountController@dashboardSendMessage');


	// AssignmentSubject
	Route::get('manageAssignmentSubject', 'AssignmentSubjectController@show');
	Route::get('createAssignmentSubject', 'AssignmentSubjectController@create');
	Route::post('createAssignmentSubject', 'AssignmentSubjectController@store');
	Route::get('assignmentSubject/{id}/edit', 'AssignmentSubjectController@edit');
	Route::put('updateAssignmentSubject', 'AssignmentSubjectController@update');
	Route::post('getAssignmentSubjectsByYear', 'AssignmentSubjectController@getAssignmentSubjectsByYear');
	Route::post('getAssignmentSubjectsOfGivenAssignmentByLecturer', 'AssignmentSubjectController@getAssignmentSubjectsOfGivenAssignmentByLecturer');
	Route::delete('deleteAssignmentSubject', 'AssignmentSubjectController@delete');

	// AssignmentTopic
	Route::get('manageAssignmentTopic', 'AssignmentTopicController@show');
	Route::get('createAssignmentTopic', 'AssignmentTopicController@create');
	Route::post('createAssignmentTopic', 'AssignmentTopicController@store');
	Route::get('assignmentTopic/{id}/edit', 'AssignmentTopicController@edit');
	Route::put('updateAssignmentTopic', 'AssignmentTopicController@update');
	Route::delete('deleteAssignmentTopic', 'AssignmentTopicController@delete');

	// Assignments
	Route::get('manageAssignment', 'AssignmentController@show');
	Route::get('createAssignment', 'AssignmentController@create');
	Route::post('createAssignment', 'AssignmentController@store');
	Route::get('assignment/{id}/edit', 'AssignmentController@edit');
	Route::put('updateAssignment', 'AssignmentController@update');
	Route::post('getAssignmentTopics', 'AssignmentController@getAssignmentTopics');
	Route::post('getAssignmentByTopic', 'AssignmentController@getAssignmentByTopic');
	Route::post('getAssignments', 'AssignmentController@getAssignments');
	Route::post('getAssignmentByTopicForStudent', 'AssignmentController@getAssignmentByTopicForStudent');
	Route::post('checkAssignmentIsExist', 'AssignmentController@checkAssignmentIsExist');
	Route::delete('deleteAssignment', 'AssignmentController@delete');

	// workshop category
	Route::get('admin/manageWorkshopCategory', 'Workshop\WorkshopCategoryController@show');
	Route::get('admin/createWorkshopCategory', 'Workshop\WorkshopCategoryController@create');
	Route::post('admin/createWorkshopCategory', 'Workshop\WorkshopCategoryController@store');
	Route::get('admin/workshopCategory/{id}/edit', 'Workshop\WorkshopCategoryController@edit');
	Route::put('admin/updateWorkshopCategory', 'Workshop\WorkshopCategoryController@update');
	Route::delete('admin/deleteWorkshopCategory', 'Workshop\WorkshopCategoryController@delete');
	Route::post('admin/isOnlineWorkshopCategoryExist', 'Workshop\WorkshopCategoryController@isOnlineWorkshopCategoryExist');

	// workshop Details
	Route::get('admin/manageWorkshopDetails', 'Workshop\WorkshopDetailsController@show');
	Route::get('admin/createWorkshopDetails', 'Workshop\WorkshopDetailsController@create');
	Route::post('admin/createWorkshopDetails', 'Workshop\WorkshopDetailsController@store');
	Route::get('admin/workshopDetails/{id}/edit', 'Workshop\WorkshopDetailsController@edit');
	Route::put('admin/updateWorkshopDetails', 'Workshop\WorkshopDetailsController@update');
	Route::delete('admin/deleteWorkshopDetails', 'Workshop\WorkshopDetailsController@delete');
	Route::post('admin/isOnlineWorkshopExist', 'Workshop\WorkshopDetailsController@isOnlineWorkshopExist');

	// workshop Videos
	Route::get('admin/manageWorkshopVideos', 'Workshop\WorkshopVideosController@show');
	Route::get('admin/createWorkshopVideo', 'Workshop\WorkshopVideosController@create');
	Route::post('admin/createWorkshopVideo', 'Workshop\WorkshopVideosController@store');
	Route::get('admin/workshopVideo/{id}/edit', 'Workshop\WorkshopVideosController@edit');
	Route::put('admin/updateWorkshopVideo', 'Workshop\WorkshopVideosController@update');
	Route::post('admin/getWorkshopsByCategory', 'Workshop\WorkshopVideosController@getWorkshopsByCategory');
	Route::delete('admin/deleteWorkshopVideo', 'Workshop\WorkshopVideosController@delete');
	Route::post('admin/isOnlineWorkshopVideoExist', 'Workshop\WorkshopVideosController@isOnlineWorkshopVideoExist');

	// workshop front
	Route::get('workshops', 'WorkshopController@show');
	Route::post('getWorkshopsByCategory', 'WorkshopController@getWorkshopsByCategory');
	Route::get('workshopDetails/{id}', 'WorkshopController@workshopDetails');
	Route::get('workshopVideo/{id}', 'WorkshopController@workshopVideo');

	// placement area
	Route::get('admin/managePlacementArea', 'Placement\PlacementAreaController@show');
	Route::get('admin/createPlacementArea', 'Placement\PlacementAreaController@create');
	Route::post('admin/createPlacementArea', 'Placement\PlacementAreaController@store');
	Route::get('admin/placementArea/{id}/edit', 'Placement\PlacementAreaController@edit');
	Route::put('admin/updatePlacementArea', 'Placement\PlacementAreaController@update');
	Route::delete('admin/deletePlacementArea', 'Placement\PlacementAreaController@delete');
	Route::post('admin/isPlacementAreaExist', 'Placement\PlacementAreaController@isPlacementAreaExist');

	// placement company
	Route::get('admin/managePlacementCompany', 'Placement\PlacementCompanyController@show');
	Route::get('admin/createPlacementCompany', 'Placement\PlacementCompanyController@create');
	Route::post('admin/createPlacementCompany', 'Placement\PlacementCompanyController@store');
	Route::get('admin/placementCompany/{id}/edit', 'Placement\PlacementCompanyController@edit');
	Route::put('admin/updatePlacementCompany', 'Placement\PlacementCompanyController@update');
	Route::delete('admin/deletePlacementCompany', 'Placement\PlacementCompanyController@delete');
	Route::post('admin/isPlacementCompanyExist', 'Placement\PlacementCompanyController@isPlacementCompanyExist');

	// placement Details
	Route::get('admin/managePlacementCompanyDetails', 'Placement\PlacementCompanyDetailsController@show');
	Route::get('admin/createPlacementCompanyDetails', 'Placement\PlacementCompanyDetailsController@create');
	Route::post('admin/createPlacementCompanyDetails', 'Placement\PlacementCompanyDetailsController@store');
	Route::get('admin/placementCompanyDetail/{id}/edit', 'Placement\PlacementCompanyDetailsController@edit');
	Route::put('admin/updatePlacementCompanyDetails', 'Placement\PlacementCompanyDetailsController@update');
	Route::post('admin/getPlacementCompaniesByArea', 'Placement\PlacementCompanyDetailsController@getPlacementCompaniesByArea');
	Route::post('admin/checkCompanyDetails', 'Placement\PlacementCompanyDetailsController@checkCompanyDetails');
	Route::delete('admin/deleteCompanyDetails', 'Placement\PlacementCompanyDetailsController@delete');


	// placement process
	Route::get('admin/managePlacementProcess', 'Placement\PlacementProcessController@show');
	Route::get('admin/createPlacementProcess', 'Placement\PlacementProcessController@create');
	Route::post('admin/createPlacementProcess', 'Placement\PlacementProcessController@store');
	Route::get('admin/placementCompanyProcess/{id}/edit', 'Placement\PlacementProcessController@edit');
	Route::put('admin/updatePlacementProcess', 'Placement\PlacementProcessController@update');
	Route::post('admin/checkPlacementCompanyProcesss', 'Placement\PlacementProcessController@checkPlacementCompanyProcesss');
	Route::delete('admin/deletePlacementProcess', 'Placement\PlacementProcessController@delete');


	// placement front
	Route::get('placements', 'PlacementController@show');
	Route::post('placements', 'PlacementController@showPlacements');
	Route::post('getPlacementCompaniesByArea', 'PlacementController@getPlacementCompaniesByArea');
	Route::post('getPlacementCompaniesByAreaForFront', 'PlacementController@getPlacementCompaniesByAreaForFront');
	Route::post('createPlacementExperiance', 'PlacementController@createPlacementExperiance');
	Route::get('placementExperiance/{id}', 'PlacementController@placementExperiance');
	Route::post('createPlacementProcessComment', 'PlacementController@createPlacementProcessComment');
	Route::post('updatePlacementProcessComment', 'PlacementController@createPlacementProcessComment');
	Route::post('createPlacementProcessSubComment', 'PlacementController@createPlacementProcessSubComment');
	Route::post('deletePlacementProcessComment', 'PlacementController@deletePlacementProcessComment');
	Route::post('updatePlacementProcessSubComment', 'PlacementController@updatePlacementProcessSubComment');
	Route::post('deletePlacementProcessSubComment', 'PlacementController@deletePlacementProcessSubComment');
	Route::post('likePlacementProcessComment', 'PlacementController@likePlacementProcessComment');
	Route::post('likePlacementProcessSubComment', 'PlacementController@likePlacementProcessSubComment');
	Route::post('likePlacementProcess', 'PlacementController@likePlacementProcess');

	// placement faq
	Route::get('admin/managePlacementFaq', 'Placement\PlacementFaqController@show');
	Route::get('admin/createPlacementFaq', 'Placement\PlacementFaqController@create');
	Route::post('admin/createPlacementFaq', 'Placement\PlacementFaqController@store');
	Route::get('admin/placementFaq/{id}/edit', 'Placement\PlacementFaqController@edit');
	Route::put('admin/updatePlacementFaq', 'Placement\PlacementFaqController@update');
	Route::delete('admin/deletePlacementFaq', 'Placement\PlacementFaqController@delete');

	// placement ApplyJob
	Route::get('admin/manageApplyJob', 'Placement\PlacementApplyJobController@show');
	Route::get('admin/createApplyJob', 'Placement\PlacementApplyJobController@create');
	Route::post('admin/createApplyJob', 'Placement\PlacementApplyJobController@store');
	Route::get('admin/applyJob/{id}/edit', 'Placement\PlacementApplyJobController@edit');
	Route::put('admin/updateApplyJob', 'Placement\PlacementApplyJobController@update');
	Route::delete('admin/deleteApplyJob', 'Placement\PlacementApplyJobController@delete');

	// offline workshop category
	Route::get('admin/manageOfflineWorkshopCategory', 'OfflineWorkshop\OfflineWorkshopCategoryController@show');
	Route::get('admin/createOfflineWorkshopCategory', 'OfflineWorkshop\OfflineWorkshopCategoryController@create');
	Route::post('admin/createOfflineWorkshopCategory', 'OfflineWorkshop\OfflineWorkshopCategoryController@store');
	Route::get('admin/offlineWorkshopCategory/{id}/edit', 'OfflineWorkshop\OfflineWorkshopCategoryController@edit');
	Route::put('admin/updateOfflineWorkshopCategory', 'OfflineWorkshop\OfflineWorkshopCategoryController@update');
	Route::delete('admin/deleteOfflineWorkshopCategory', 'OfflineWorkshop\OfflineWorkshopCategoryController@delete');
	Route::post('admin/isOfflineWorkshopCategoryExist', 'OfflineWorkshop\OfflineWorkshopCategoryController@isOfflineWorkshopCategoryExist');


	// offline workshop Details
	Route::get('admin/manageOfflineWorkshopDetails', 'OfflineWorkshop\OfflineWorkshopDetailsController@show');
	Route::get('admin/createOfflineWorkshopDetails', 'OfflineWorkshop\OfflineWorkshopDetailsController@create');
	Route::post('admin/createOfflineWorkshopDetails', 'OfflineWorkshop\OfflineWorkshopDetailsController@store');
	Route::get('admin/offlineWorkshopDetails/{id}/edit', 'OfflineWorkshop\OfflineWorkshopDetailsController@edit');
	Route::put('admin/updateOfflineWorkshopDetails', 'OfflineWorkshop\OfflineWorkshopDetailsController@update');
	Route::delete('admin/deleteOfflineWorkshopDetails', 'OfflineWorkshop\OfflineWorkshopDetailsController@delete');
	Route::post('admin/isOfflineWorkshopExist', 'OfflineWorkshop\OfflineWorkshopDetailsController@isOfflineWorkshopExist');


	// front offline workshop
	Route::get('offlineworkshops', 'OfflineWorkshopController@show');
	Route::get('offlineworkshopdetails/{id}', 'OfflineWorkshopController@offlineWorkshopDetails');
	Route::post('workshopquery', 'OfflineWorkshopController@workshopQuery');
	Route::post('getOfflineWorkshopsByCategory', 'OfflineWorkshopController@getOfflineWorkshopsByCategory');

	// Motivational Speech Category
	Route::get('admin/manageMotivationalSpeechCategory', 'MotivationalSpeech\MotivationalSpeechCategoryController@show');
	Route::get('admin/createMotivationalSpeechCategory', 'MotivationalSpeech\MotivationalSpeechCategoryController@create');
	Route::post('admin/createMotivationalSpeechCategory', 'MotivationalSpeech\MotivationalSpeechCategoryController@store');
	Route::get('admin/motivationalSpeechCategory/{id}/edit', 'MotivationalSpeech\MotivationalSpeechCategoryController@edit');
	Route::put('admin/updateMotivationalSpeechCategory', 'MotivationalSpeech\MotivationalSpeechCategoryController@update');
	Route::delete('admin/deleteMotivationalSpeechCategory', 'MotivationalSpeech\MotivationalSpeechCategoryController@delete');
	Route::post('admin/isMotivationalSpeechCategoryExist', 'MotivationalSpeech\MotivationalSpeechCategoryController@isMotivationalSpeechCategoryExist');


	// Motivational Speech Details
	Route::get('admin/manageMotivationalSpeechDetails', 'MotivationalSpeech\MotivationalSpeechDetailsController@show');
	Route::get('admin/createMotivationalSpeechDetails', 'MotivationalSpeech\MotivationalSpeechDetailsController@create');
	Route::post('admin/createMotivationalSpeechDetails', 'MotivationalSpeech\MotivationalSpeechDetailsController@store');
	Route::get('admin/motivationalSpeechDetails/{id}/edit', 'MotivationalSpeech\MotivationalSpeechDetailsController@edit');
	Route::put('admin/updateMotivationalSpeechDetails', 'MotivationalSpeech\MotivationalSpeechDetailsController@update');
	Route::delete('admin/deleteMotivationalSpeechDetails', 'MotivationalSpeech\MotivationalSpeechDetailsController@delete');
	Route::post('admin/isMotivationalSpeechExist', 'MotivationalSpeech\MotivationalSpeechDetailsController@isMotivationalSpeechExist');
	Route::post('admin/getMotivationalSpeechesByCategoryByAdmin', 'MotivationalSpeech\MotivationalSpeechDetailsController@getMotivationalSpeechesByCategoryByAdmin');

	// Motivational Speech Video
	Route::get('admin/manageMotivationalSpeechVideos', 'MotivationalSpeech\MotivationalSpeechVideoController@show');
	Route::get('admin/createMotivationalSpeechVideo', 'MotivationalSpeech\MotivationalSpeechVideoController@create');
	Route::post('admin/createMotivationalSpeechVideo', 'MotivationalSpeech\MotivationalSpeechVideoController@store');
	Route::get('admin/motivationalSpeechVideo/{id}/edit', 'MotivationalSpeech\MotivationalSpeechVideoController@edit');
	Route::put('admin/updateMotivationalSpeechVideo', 'MotivationalSpeech\MotivationalSpeechVideoController@update');
	Route::delete('admin/deleteMotivationalSpeechVideo', 'MotivationalSpeech\MotivationalSpeechVideoController@delete');
	Route::post('admin/isMotivationalSpeechVideoExist', 'MotivationalSpeech\MotivationalSpeechVideoController@isMotivationalSpeechVideoExist');


	// Motional speech front
	Route::get('motivationalspeech', 'MotivationalSpeechController@show');
	Route::post('getMotivationalSpeechesByCategory', 'MotivationalSpeechController@getMotivationalSpeechesByCategory');
	Route::get('motivationalSpeechDetails/{id}', 'MotivationalSpeechController@motivationalSpeechDetails');
	Route::post('motivationalspeechquery', 'MotivationalSpeechController@motivationalspeechquery');

	// virtual placement drive
	Route::get('admin/manageVirtualPlacementDrive', 'VirtualPlacement\VirtualPlacementDriveController@show');
	Route::get('admin/createVirtualPlacementDrive', 'VirtualPlacement\VirtualPlacementDriveController@create');
	Route::post('admin/createVirtualPlacementDrive', 'VirtualPlacement\VirtualPlacementDriveController@store');
	Route::get('admin/virtualPlacementDrive/{id}/edit', 'VirtualPlacement\VirtualPlacementDriveController@edit');
	Route::put('admin/updateVirtualPlacementDrive', 'VirtualPlacement\VirtualPlacementDriveController@update');
	Route::delete('admin/deleteVirtualPlacementDrive', 'VirtualPlacement\VirtualPlacementDriveController@delete');

	// admin advertisement page
	Route::get('admin/manageAdvertisementPages', 'AdvertisementPage\AdvertisementPageController@show');
	Route::get('admin/createAdvertisementPage', 'AdvertisementPage\AdvertisementPageController@create');
	Route::post('admin/createAdvertisementPage', 'AdvertisementPage\AdvertisementPageController@store');
	Route::get('admin/advertisementPage/{id}/edit', 'AdvertisementPage\AdvertisementPageController@edit');
	Route::put('admin/updateAdvertisementPage', 'AdvertisementPage\AdvertisementPageController@update');
	Route::delete('admin/deleteAdvertisementPage', 'AdvertisementPage\AdvertisementPageController@delete');

	// company test
	Route::get('companyTest/{id?}', 'CompanyTestController@index');
	Route::get('mockInterview', 'CompanyTestController@mockInterview');
	Route::post('getSelectedStudentBySkillId', 'CompanyTestController@getSelectedStudentBySkillId');

	// Skills
	Route::get('admin/manageSkill', 'Admin\SkillController@show');
	Route::get('admin/createSkill', 'Admin\SkillController@create');
	Route::post('admin/createSkill', 'Admin\SkillController@store');
	Route::get('admin/skill/{id}/edit', 'Admin\SkillController@edit');
	Route::put('admin/updateSkill', 'Admin\SkillController@update');
	Route::delete('admin/deleteSkill', 'Admin\SkillController@delete');
	Route::post('admin/isSkillExist', 'Admin\SkillController@isSkillExist');

	// UserData
	Route::get('admin/manageUserData', 'Admin\UserDataController@show');
	Route::get('admin/createUserData', 'Admin\UserDataController@create');
	Route::post('admin/createUserData', 'Admin\UserDataController@store');
	Route::get('admin/userData/{id}/edit', 'Admin\UserDataController@edit');
	Route::put('admin/updateUserData', 'Admin\UserDataController@update');
	Route::delete('admin/deleteUserData', 'Admin\UserDataController@delete');
	Route::post('admin/verifyUserByEmailIdByPaperId', 'Admin\UserDataController@verifyUserByEmailIdByPaperId');
});

Route::group(['domain' => '{client}.localvchip.com'], function () {
	Route::get('/', 'Client\ClientHomeController@clientHome');
	Route::get('client/login', 'ClientAuth\LoginController@showLoginForm');
  	Route::post('client/login', 'ClientAuth\LoginController@login');
  	Route::post('client/logout', 'ClientAuth\LoginController@logout');

	Route::get('/auth/{provider}', 'SocialiteController@subdomainRedirectToProvider');
	Route::get('/auth/{provider}/callback', 'SocialiteController@handleProviderCallback');

  	// verify account
  	Route::get('verifyAccount', 'Client\ClientHomeController@verifyAccount');
  	Route::post('verifyClientEmail', 'Client\ClientHomeController@verifyClientEmail');
  	// Route::get('clientforgotPassword', 'Client\ClientHomeController@clientforgotPassword');
  	// Route::post('clientforgotPassword', 'Client\ClientHomeController@clientforgotPassword');
  	Route::post('sendClientUserSignUpOtp', 'Client\ClientHomeController@sendClientUserSignUpOtp');
  	Route::post('sendClientUserSignInOtp', 'Client\ClientHomeController@sendClientUserSignInOtp');
  	Route::post('sendClientUserParentSignInOtp', 'Client\ClientHomeController@sendClientUserParentSignInOtp');

  	Route::get('clientforgotPassword', 'ClientAuth\ForgotPasswordController@showLinkRequestForm');
  	Route::post('clientpassword/email', 'ClientAuth\ForgotPasswordController@sendPasswordResetLink');
  	Route::get('clientpassword/reset/{token}', 'ClientAuth\ResetPasswordController@showResetForm');
  	Route::post('client/password/reset', 'ClientAuth\ResetPasswordController@reset');
  	Route::get('parentLogin', 'Client\ClientHomeController@parentLogin');
  	Route::post('parentLogin', 'Client\ClientHomeController@loginParent');

  	// online client
  	Route::get('digitaleducation', 'Client\OnlineClientController@digitaleducation');
  	Route::get('webdevelopment', 'Client\OnlineClientController@webdevelopment');
  	Route::get('digitalmarketing', 'Client\OnlineClientController@digitalmarketing');
  	Route::get('pricing', 'Client\OnlineClientController@pricing');
  	Route::get('getWebdevelopment', 'Client\OnlineClientController@getWebdevelopment');
  	Route::post('doWebdevelopmentPayment', 'Client\OnlineClientController@doWebdevelopmentPayment');
  	Route::get('thankyouwebdevelopment', 'Client\OnlineClientController@thankyouwebdevelopment');
	Route::post('webhookwebdevelopment', 'Client\OnlineClientController@webhookwebdevelopment');
	Route::post('sendContactUsMail', 'Client\OnlineClientController@sendContactUsMail');
	Route::get('clientsignup/{planId}', 'Client\OnlineClientController@clientsignup');
	Route::post('isCLientExists', 'Client\OnlineClientController@isCLientExists');
	Route::post('doPayment', 'Client\OnlineClientController@doPayment');
	Route::get('thankyouclient', 'Client\OnlineClientController@thankyouclient');
	Route::any('webhookclient', 'Client\OnlineClientController@webhookclient');
	Route::post('freeRegister', 'Client\OnlineClientController@freeRegister');

  	// client users info
  	Route::get('allUsers', 'Client\ClientUsersInfoController@allUsers');
  	Route::post('searchUsers', 'Client\ClientUsersInfoController@searchUsers');
  	Route::post('changeClientUserApproveStatus', 'Client\ClientUsersInfoController@changeClientUserApproveStatus');
  	Route::post('deleteStudent', 'Client\ClientUsersInfoController@deleteStudent');
  	Route::post('changeClientUserCourseStatus', 'Client\ClientUsersInfoController@changeClientUserCourseStatus');
  	Route::post('changeClientUserTestSubCategoryStatus', 'Client\ClientUsersInfoController@changeClientUserTestSubCategoryStatus');
  	Route::get('userTestResults/{id?}', 'Client\ClientUsersInfoController@userTestResults');
  	Route::post('showUserTestResults', 'Client\ClientUsersInfoController@showUserTestResults');
  	Route::get('userCourses/{id?}', 'Client\ClientUsersInfoController@userCourses');
  	Route::post('showUserCourses', 'Client\ClientUsersInfoController@showUserCourses');
  	Route::get('userPlacement/{id?}', 'Client\ClientUsersInfoController@userPlacement');
  	Route::post('getStudentById', 'Client\ClientUsersInfoController@getStudentById');
  	Route::get('userVideo/{id?}', 'Client\ClientUsersInfoController@userVideo');
	Route::put('updateUserVideo', 'Client\ClientUsersInfoController@updateUserVideo');
	Route::get('allTestResults', 'Client\ClientUsersInfoController@allTestResults');
	Route::post('getAllTestResults', 'Client\ClientUsersInfoController@getAllTestResults');
	Route::get('downloadExcelResult', 'Client\ClientUsersInfoController@downloadExcelResult');
	Route::get('myprofile', 'Client\ClientUsersInfoController@profile');
	Route::put('updateClientProfile', 'Client\ClientUsersInfoController@updateClientProfile');
	Route::put('updateClientPassword', 'Client\ClientUsersInfoController@updateClientPassword');
	Route::get('manageSettings', 'Client\ClientUsersInfoController@manageSettings');
	Route::post('toggleNonVerifiedEmailStatus', 'Client\ClientUsersInfoController@toggleNonVerifiedEmailStatus');
	Route::get('addUsers', 'Client\ClientUsersInfoController@addUsers');
	Route::post('addMobileUser', 'Client\ClientUsersInfoController@addMobileUser');
	Route::post('addEmailUser', 'Client\ClientUsersInfoController@addEmailUser');
	Route::post('uploadClientUsers', 'Client\ClientUsersInfoController@uploadClientUsers');
	Route::post('changeClientSetting', 'Client\ClientUsersInfoController@changeClientSetting');

  	// register client user
  	Route::post('/register', 'ClientuserAuth\RegisterController@register');
  	// Route::post('/registerByMobile', 'ClientuserAuth\RegisterController@registerByMobile');
  	Route::post('/login', 'ClientuserAuth\LoginController@login');
  	Route::post('clientUserLogin', 'ClientuserAuth\LoginController@clientUserLogin');
	Route::post('/logout', 'ClientuserAuth\LoginController@logout');
	Route::get('register/verify/{token}', 'ClientuserAuth\RegisterController@verify');
	Route::get('forgotPassword', 'ClientuserAuth\ForgotPasswordController@showLinkRequestForm');
	Route::post('password/email', 'ClientuserAuth\ForgotPasswordController@sendPasswordResetLink');
	Route::get('password/reset/{token}', 'ClientuserAuth\ResetPasswordController@showResetForm');
	Route::post('clientuser/password/reset', 'ClientuserAuth\ResetPasswordController@reset');

  	// Route::get('client/home', 'Client\ClientBaseController@showDashBoard');
  	Route::get('manageClientHome', 'Client\ClientBaseController@manageClientHome');
	Route::put('updateClientHome', 'Client\ClientBaseController@updateClientHome');
	Route::get('managePlans', 'Client\ClientBaseController@managePlans');
	Route::get('manageBillings', 'Client\ClientBaseController@manageBillings');
	Route::get('manageHistory', 'Client\ClientBaseController@manageHistory');
	Route::get('thankyou', 'Client\ClientBaseController@thankyou');
	Route::any('webhook', 'Client\ClientBaseController@webhook');
	Route::post('degradePayment', 'Client\ClientBaseController@degradePayment');
	Route::post('upgradePayment', 'Client\ClientBaseController@upgradePayment');
	Route::post('continuePayment', 'Client\ClientBaseController@continuePayment');
	Route::post('deactivatePlan', 'Client\ClientBaseController@deactivatePlan');
	Route::get('manageBankDetails', 'Client\ClientBaseController@manageBankDetails');
	Route::post('updateBankDetails', 'Client\ClientBaseController@updateBankDetails');
	Route::get('manageUserPayments', 'Client\ClientBaseController@manageUserPayments');
	Route::post('getClientUserPayments', 'Client\ClientBaseController@getClientUserPayments');
	Route::post('searchContact', 'Client\ClientBaseController@searchContact');
	Route::get('allChatMessages', 'Client\ClientBaseController@allChatMessages');
	Route::post('dashboardPrivateChat', 'Client\ClientBaseController@dashboardPrivateChat');
	Route::post('dashboardSendMessage', 'Client\ClientBaseController@dashboardSendMessage');
	Route::post('getContacts', 'Client\ClientBaseController@getContacts');

  	// category
  	Route::get('manageOnlineCategory', 'Client\OnlineCourse\ClientOnlineCategoryController@show');
  	Route::get('createOnlineCategory', 'Client\OnlineCourse\ClientOnlineCategoryController@create');
  	Route::post('createOnlineCategory', 'Client\OnlineCourse\ClientOnlineCategoryController@store');
  	Route::get('onlinecategory/{id}/edit', 'Client\OnlineCourse\ClientOnlineCategoryController@edit');
  	Route::put('updateOnlineCategory', 'Client\OnlineCourse\ClientOnlineCategoryController@update');
  	Route::delete('deleteOnlineCategory', 'Client\OnlineCourse\ClientOnlineCategoryController@delete');
  	Route::post('isClientCourseCategoryExist', 'Client\OnlineCourse\ClientOnlineCategoryController@isClientCourseCategoryExist');

  	// sub category
  	Route::get('manageOnlineSubCategory', 'Client\OnlineCourse\ClientOnlineSubCategoryController@show');
  	Route::get('createOnlineSubCategory', 'Client\OnlineCourse\ClientOnlineSubCategoryController@create');
  	Route::post('createOnlineSubCategory', 'Client\OnlineCourse\ClientOnlineSubCategoryController@store');
  	Route::get('onlinesubcategory/{id}/edit', 'Client\OnlineCourse\ClientOnlineSubCategoryController@edit');
  	Route::put('updateOnlineSubCategory', 'Client\OnlineCourse\ClientOnlineSubCategoryController@update');
  	Route::delete('deleteOnlineSubCategory', 'Client\OnlineCourse\ClientOnlineSubCategoryController@delete');
  	Route::post('getOnlineCategories', 'Client\OnlineCourse\ClientOnlineSubCategoryController@getOnlineCategories');
  	Route::post('isClientCourseSubCategoryExist', 'Client\OnlineCourse\ClientOnlineSubCategoryController@isClientCourseSubCategoryExist');

  	// Online course
  	Route::get('manageOnlineCourse', 'Client\OnlineCourse\ClientOnlineCourseController@show');
  	Route::get('createOnlineCourse', 'Client\OnlineCourse\ClientOnlineCourseController@create');
  	Route::post('createOnlineCourse', 'Client\OnlineCourse\ClientOnlineCourseController@store');
  	Route::get('onlinecourse/{id}/edit', 'Client\OnlineCourse\ClientOnlineCourseController@edit');
  	Route::put('updateOnlineCourse', 'Client\OnlineCourse\ClientOnlineCourseController@update');
  	Route::delete('deleteOnlineCourse', 'Client\OnlineCourse\ClientOnlineCourseController@delete');
	Route::post('getOnlineCourseByCatIdBySubCatIdForClient', 'Client\OnlineCourse\ClientOnlineCourseController@getOnlineCourseByCatIdBySubCatIdForClient');
	Route::post('isClientOnlineCourseExist', 'Client\OnlineCourse\ClientOnlineCourseController@isClientOnlineCourseExist');

  	// Online video
  	Route::get('manageOnlineVideo', 'Client\OnlineCourse\ClientOnlineVideoController@show');
  	Route::get('createOnlineVideo', 'Client\OnlineCourse\ClientOnlineVideoController@create');
  	Route::post('createOnlineVideo', 'Client\OnlineCourse\ClientOnlineVideoController@store');
  	Route::get('onlinevideo/{id}/edit', 'Client\OnlineCourse\ClientOnlineVideoController@edit');
  	Route::put('updateOnlineVideo', 'Client\OnlineCourse\ClientOnlineVideoController@update');
  	Route::delete('deleteOnlineVideo', 'Client\OnlineCourse\ClientOnlineVideoController@delete');
  	Route::post('isClientCourseVideoExist', 'Client\OnlineCourse\ClientOnlineVideoController@isClientCourseVideoExist');

  	//manageAllCourse
  	Route::get('manageAllCourse', 'Client\OnlineCourse\ClientAllCourseController@showAll');
  	Route::post('createAllCourseCategory', 'Client\OnlineCourse\ClientAllCourseController@storeCategory');
  	Route::post('createAllCourseSubCategory', 'Client\OnlineCourse\ClientAllCourseController@storeSubCategory');
  	Route::post('createAllCourseCourse', 'Client\OnlineCourse\ClientAllCourseController@storeCourse');

  	// manage All test
  	Route::get('manageAllTest', 'Client\OnlineTest\ClientAllTestController@showAll');
  	Route::post('createAllTestCategory', 'Client\OnlineTest\ClientAllTestController@storeCategory');
  	Route::post('createAllTestSubCategory', 'Client\OnlineTest\ClientAllTestController@storeSubCategory');
  	Route::post('createAllTestSubject', 'Client\OnlineTest\ClientAllTestController@storeSubject');
  	Route::post('createAllTestPaper', 'Client\OnlineTest\ClientAllTestController@storePaper');

  	// test category
  	Route::get('manageOnlineTestCategory', 'Client\OnlineTest\ClientOnlineTestCategoryController@show');
  	Route::get('createOnlineTestCategory', 'Client\OnlineTest\ClientOnlineTestCategoryController@create');
  	Route::post('createOnlineTestCategory', 'Client\OnlineTest\ClientOnlineTestCategoryController@store');
  	Route::get('onlinetestcategory/{id}/edit', 'Client\OnlineTest\ClientOnlineTestCategoryController@edit');
  	Route::put('updateOnlineTestCategory', 'Client\OnlineTest\ClientOnlineTestCategoryController@update');
  	Route::delete('deleteOnlineTestCategory', 'Client\OnlineTest\ClientOnlineTestCategoryController@delete');
  	Route::post('isClientTestCategoryExist', 'Client\OnlineTest\ClientOnlineTestCategoryController@isClientTestCategoryExist');


  	// test sub category
  	Route::get('manageOnlineTestSubCategory', 'Client\OnlineTest\ClientOnlineTestSubCategoryController@show');
  	Route::get('createOnlineTestSubCategory', 'Client\OnlineTest\ClientOnlineTestSubCategoryController@create');
  	Route::post('createOnlineTestSubCategory', 'Client\OnlineTest\ClientOnlineTestSubCategoryController@store');
  	Route::get('onlinetestsubcategory/{id}/edit', 'Client\OnlineTest\ClientOnlineTestSubCategoryController@edit');
  	Route::put('updateOnlineTestSubCategory', 'Client\OnlineTest\ClientOnlineTestSubCategoryController@update');
  	Route::delete('deleteOnlineTestSubCategory', 'Client\OnlineTest\ClientOnlineTestSubCategoryController@delete');
  	Route::post('getOnlineTestCategories', 'Client\OnlineTest\ClientOnlineTestSubCategoryController@getOnlineTestCategories');
  	Route::post('isClientTestSubCategoryExist', 'Client\OnlineTest\ClientOnlineTestSubCategoryController@isClientTestSubCategoryExist');

  	// test subject
  	Route::get('manageOnlineTestSubject', 'Client\OnlineTest\ClientOnlineTestSubjectController@show');
  	Route::get('createOnlineTestSubject', 'Client\OnlineTest\ClientOnlineTestSubjectController@create');
  	Route::post('createOnlineTestSubject', 'Client\OnlineTest\ClientOnlineTestSubjectController@store');
  	Route::get('onlinetestsubject/{id}/edit', 'Client\OnlineTest\ClientOnlineTestSubjectController@edit');
  	Route::put('updateOnlineTestSubject', 'Client\OnlineTest\ClientOnlineTestSubjectController@update');
  	Route::delete('deleteOnlineTestSubject', 'Client\OnlineTest\ClientOnlineTestSubjectController@delete');
	Route::post('getOnlineSubjectsByCatIdBySubcatId', 'Client\OnlineTest\ClientOnlineTestSubjectController@getOnlineSubjectsByCatIdBySubcatId');
	Route::post('isClientTestSubjectExist', 'Client\OnlineTest\ClientOnlineTestSubjectController@isClientTestSubjectExist');

  	// test subject paper
  	Route::get('manageOnlineTestSubjectPaper', 'Client\OnlineTest\ClientOnlineTestSubjectPaperController@show');
  	Route::get('createOnlineTestSubjectPaper', 'Client\OnlineTest\ClientOnlineTestSubjectPaperController@create');
  	Route::post('createOnlineTestSubjectPaper', 'Client\OnlineTest\ClientOnlineTestSubjectPaperController@store');
  	Route::get('onlinetestsubjectpaper/{id}/edit', 'Client\OnlineTest\ClientOnlineTestSubjectPaperController@edit');
  	Route::put('updateOnlineTestSubjectPaper', 'Client\OnlineTest\ClientOnlineTestSubjectPaperController@update');
  	Route::delete('deleteOnlineTestSubjectPaper', 'Client\OnlineTest\ClientOnlineTestSubjectPaperController@delete');
  	Route::post('getOnlinePapersBySubjectId', 'Client\OnlineTest\ClientOnlineTestSubjectPaperController@getOnlinePapersBySubjectId');
  	Route::post('paperSectionsByPaperId', 'Client\OnlineTest\ClientOnlineTestSubjectPaperController@paperSectionsByPaperId');
  	Route::post('isClientTestPaperExist', 'Client\OnlineTest\ClientOnlineTestSubjectPaperController@isClientTestPaperExist');
  	Route::post('getOnlinePapersBySubjectIdWithPayable', 'Client\OnlineTest\ClientOnlineTestSubjectPaperController@getOnlinePapersBySubjectIdWithPayable');

  	// test question
  	Route::get('manageOnlineTestQuestion', 'Client\OnlineTest\ClientOnlineTestQuestionController@index');
  	Route::post('showOnlineTestQuestion', 'Client\OnlineTest\ClientOnlineTestQuestionController@show');
  	Route::get('createOnlineTestQuestion', 'Client\OnlineTest\ClientOnlineTestQuestionController@create');
  	Route::post('createOnlineTestQuestion', 'Client\OnlineTest\ClientOnlineTestQuestionController@store');
  	Route::get('onlinetestquestion/{id}/edit', 'Client\OnlineTest\ClientOnlineTestQuestionController@edit');
  	Route::put('updateOnlineTestQuestion', 'Client\OnlineTest\ClientOnlineTestQuestionController@update');
  	Route::delete('deleteOnlineTestQuestion', 'Client\OnlineTest\ClientOnlineTestQuestionController@delete');
  	Route::post('getClientNextQuestionCount', 'Client\OnlineTest\ClientOnlineTestQuestionController@getClientNextQuestionCount');
  	Route::post('getClientCurrentQuestionCount', 'Client\OnlineTest\ClientOnlineTestQuestionController@getClientCurrentQuestionCount');
  	Route::post('getClientPrevQuestion', 'Client\OnlineTest\ClientOnlineTestQuestionController@getClientPrevQuestion');
  	Route::get('manageUploadQuestions', 'Client\OnlineTest\ClientOnlineTestQuestionController@uploadQuestions');
  	Route::post('uploadQuestions', 'Client\OnlineTest\ClientOnlineTestQuestionController@importQuestions');
  	Route::post('uploadClientTestImages', 'Client\OnlineTest\ClientOnlineTestQuestionController@uploadClientTestImages');
  	Route::get('manageQuestionBank', 'Client\OnlineTest\ClientOnlineTestQuestionController@manageQuestionBank');
  	Route::post('useQuestionBank', 'Client\OnlineTest\ClientOnlineTestQuestionController@useQuestionBank');
  	Route::post('getQuestionBankSubCategories', 'Client\OnlineTest\ClientOnlineTestQuestionController@getQuestionBankSubCategories');
  	Route::post('exportQuestionBank', 'Client\OnlineTest\ClientOnlineTestQuestionController@exportQuestionBank');


  	// online courses front
	Route::get('online-courses', 'Client\Front\ClientOnlineCourseFrontController@courses');
	Route::post('getOnlineCourseByCatIdBySubCatId', 'Client\Front\ClientOnlineCourseFrontController@getOnlineCourseByCatIdBySubCatId');
	Route::get('courseDetails/{id}', 'Client\Front\ClientOnlineCourseFrontController@courseDetails');
	Route::get('episode/{id}/{subcomment?}', [ 'as' => 'client.episode', 'uses' => 'Client\Front\ClientOnlineCourseFrontController@episode' ]);
	Route::post('getOnlineSubCategoriesWithCourses', 'Client\Front\ClientOnlineCourseFrontController@getOnlineSubCategoriesWithCourses');
  	Route::post('getOnlineSubCategories', 'Client\Front\ClientOnlineCourseFrontController@getOnlineSubCategories');
  	Route::post('registerClientUserCourse', 'Client\Front\ClientOnlineCourseFrontController@registerClientUserCourse');
  	Route::post('getRegisteredOnlineCourseByCatIdBySubCatId', 'Client\Front\ClientOnlineCourseFrontController@getRegisteredOnlineCourseByCatIdBySubCatId');
  	Route::post('createClientCourseComment', 'Client\Front\ClientOnlineCourseFrontController@createClientCourseComment');
  	Route::post('updateClientCourseComment', 'Client\Front\ClientOnlineCourseFrontController@updateClientCourseComment');
  	Route::post('deleteClientCourseComment', 'Client\Front\ClientOnlineCourseFrontController@deleteClientCourseComment');
	Route::post('createClientCourseSubComment', 'Client\Front\ClientOnlineCourseFrontController@createClientCourseSubComment');
	Route::post('updateClientCourseSubComment', 'Client\Front\ClientOnlineCourseFrontController@updateClientCourseSubComment');
	Route::post('deleteClientCourseSubComment', 'Client\Front\ClientOnlineCourseFrontController@deleteClientCourseSubComment');
	Route::post('likeClientCourseVideo', 'Client\Front\ClientOnlineCourseFrontController@likeClientCourseVideo');
	Route::post('likeClientCourseVideoComment', 'Client\Front\ClientOnlineCourseFrontController@likeClientCourseVideoComment');
	Route::post('likeClientCourseVideoSubComment', 'Client\Front\ClientOnlineCourseFrontController@likeClientCourseVideoSubComment');


  	// online tests front
	Route::get('online-tests', 'Client\Front\ClientOnlineTestFrontController@tests');
	Route::post('getOnlineTestSubCategories', 'Client\Front\ClientOnlineTestFrontController@getOnlineTestSubCategories');
	Route::get('getTest/{id}/{subject?}/{paper?}', 'Client\Front\ClientOnlineTestFrontController@getTest');
	Route::post('getOnlineTestSubcategoriesByCategoryIdAssociatedWithQuestion', 'Client\Front\ClientOnlineTestFrontController@getOnlineTestSubcategoriesByCategoryIdAssociatedWithQuestion');
	Route::post('getOnlineSubjectsAndPapersByCatIdBySubcatIdAssociatedWithQuestion', 'Client\Front\ClientOnlineTestFrontController@getOnlineSubjectsAndPapersByCatIdBySubcatIdAssociatedWithQuestion');
	Route::post('setClientUserSessions', 'Client\Front\ClientOnlineTestFrontController@setClientUserSessions');
	Route::get('instructions', 'Client\Front\ClientOnlineTestFrontController@showInstructions');
	Route::post('registerClientUserPaper', 'Client\Front\ClientOnlineTestFrontController@registerClientUserPaper');
	Route::post('showUserTestResult', 'Client\Front\ClientOnlineTestFrontController@showUserTestResult');
	Route::post('isTestGiven', 'Client\Front\ClientOnlineTestFrontController@isTestGiven');
	Route::post('getRegisteredSubjectsAndPapersByCatIdBySubcatId', 'Client\Front\ClientOnlineTestFrontController@getRegisteredSubjectsAndPapersByCatIdBySubcatId');
	Route::post('getOnlineTestSubcategoriesWithPapers', 'Client\Front\ClientOnlineTestFrontController@getOnlineTestSubcategoriesWithPapers');
	Route::post('getOnlineTestSubCategoriesForTestResult', 'Client\Front\ClientOnlineTestFrontController@getOnlineTestSubCategoriesForTestResult');

	// client online question front
	Route::post('questions', 'Client\Front\ClientOnlineQuestionFrontController@getQuestions');
	Route::post('quiz-result', 'Client\Front\ClientOnlineQuestionFrontController@getResult');
	Route::post('solutions', 'Client\Front\ClientOnlineQuestionFrontController@getSolutions');
	Route::post('getQuestions', 'Client\Front\ClientOnlineQuestionFrontController@getAllQuestions');
	Route::post('showUserTestSolution', 'Client\Front\ClientOnlineQuestionFrontController@showUserTestSolution');
	Route::get('downloadQuestions/{category}/{subcategory}/{subject}/{paper}', 'Client\Front\ClientOnlineQuestionFrontController@downloadQuestions');


	// client user dashboard
	Route::get('dashboard', 'Client\ClientUserController@showClientUserDashBoard');
	Route::get('myCourses', 'Client\ClientUserController@myCourses');
	Route::get('myCertificate', 'Client\ClientUserController@myCertificate');
	Route::get('myTest', 'Client\ClientUserController@myTest');
	Route::get('myCourseResults', 'Client\ClientUserController@myCourseResults');
	Route::post('getCourseByCatIdBySubCatIdByUserId', 'Client\ClientUserController@getCourseByCatIdBySubCatIdByUserId');
	Route::get('myTestResults', 'Client\ClientUserController@myTestResults');
	Route::post('showUserTestResultsByCategoryBySubcategoryByUserId','Client\ClientUserController@showUserTestResultsByCategoryBySubcategoryByUserId');
  	Route::get('profile', 'Client\ClientUserController@profile');
  	Route::put('updateProfile', 'Client\ClientUserController@updateProfile');
  	Route::get('clientMessages', 'Client\ClientUserController@clientMessages');
  	Route::get('myNotifications', 'Client\ClientUserController@myNotifications');
  	Route::post('showClientMessages', 'Client\ClientUserController@clientMessages');
  	Route::get('myAssignments', 'Client\ClientUserController@myAssignments');
  	Route::post('getAssignmentSubjectsByCourseForUser', 'Client\ClientUserController@getAssignmentSubjectsByCourse');
  	Route::post('getAssignmentTopicsBySubjectForUser', 'Client\ClientUserController@getAssignmentTopicsBySubject');
  	Route::post('getAssignments', 'Client\ClientUserController@getAssignments');
  	Route::get('doAssignment/{id}', 'Client\ClientUserController@doAssignment');
  	Route::post('createAssignmentAnswer', 'Client\ClientUserController@createAssignmentAnswer');
  	Route::get('purchaseCourse/{courseId}', 'Client\ClientUserController@purchaseCourse');
  	Route::get('redirectCoursePayment', 'Client\ClientUserController@redirectCoursePayment');
  	Route::post('webhook', 'Client\ClientUserController@webhook');
  	Route::get('purchaseTestSubCategory/{subCategoryId}', 'Client\ClientUserController@purchaseTestSubCategory');
  	Route::get('redirectTestSubCategoryPayment', 'Client\ClientUserController@redirectTestSubCategoryPayment');
  	Route::post('getClientUserTestSubcategoriesBycategoryId', 'Client\ClientUserController@getClientUserTestSubcategoriesBycategoryId');
  	Route::get('myAttendance', 'Client\ClientUserController@myAttendance');
  	Route::get('getAttendance', 'Client\ClientUserController@myAttendance');
  	Route::get('myOfflineTestResults', 'Client\ClientUserController@myOfflineTestResults');
  	Route::post('showUserOfflineTestResultsByBatchIdByUserId', 'Client\ClientUserController@showUserOfflineTestResultsByBatchIdByUserId');
  	Route::get('myMessage', 'Client\ClientUserController@myMessage');
  	Route::post('addEmail', 'Client\ClientUserController@addEmail');
  	Route::post('verifyEmail', 'Client\ClientUserController@verifyEmail');
  	Route::put('updatePassword', 'Client\ClientUserController@updatePassword');
  	Route::post('sendClientUserOtp', 'Client\ClientUserController@sendClientUserOtp');
  	Route::post('updateMobile', 'Client\ClientUserController@updateMobile');
  	Route::post('verifyMobile', 'Client\ClientUserController@verifyMobile');
  	Route::get('myOfflinePayments', 'Client\ClientUserController@myOfflinePayments');
  	Route::post('getOfflinePaymentsByBatchIdByUserId', 'Client\ClientUserController@getOfflinePaymentsByBatchIdByUserId');
  	Route::get('myOnlinePayments', 'Client\ClientUserController@myOnlinePayments');
  	Route::get('uploadedTransactions', 'Client\ClientUserController@uploadedTransactions');
  	Route::get('createUploadTransaction', 'Client\ClientUserController@createUploadTransaction');
  	Route::post('createUploadTransaction', 'Client\ClientUserController@storeUploadTransaction');
  	Route::get('myCalendar', 'Client\ClientUserController@myCalendar');
  	Route::get('myParent', 'Client\ClientUserController@myParent');
  	Route::post('sendClientUserParentAddOtp', 'Client\ClientUserController@sendClientUserParentAddOtp');
  	Route::post('addParent', 'Client\ClientUserController@addParent');
  	Route::get('myIndividualMessage', 'Client\ClientUserController@myIndividualMessage');

	/// client user Post Comment
	Route::post('createClientAllPost',  'Client\ClientPostCommentController@createAllPost');
	Route::post('createClientAllPostComment',  'Client\ClientPostCommentController@createAllPostComment');
	Route::post('createClientAllChildComment',  'Client\ClientPostCommentController@createAllChildComment');
	Route::put('updateClientAllPost',  'Client\ClientPostCommentController@updateClientAllPost');
	Route::delete('deleteClientAllPost',  'Client\ClientPostCommentController@deleteClientAllPost');
	Route::post('createClientAllSubComment',  'Client\ClientPostCommentController@createClientAllSubComment');
	Route::put('updateClientAllSubComment',  'Client\ClientPostCommentController@updateClientAllSubComment');
	Route::delete('deleteClientAllSubComment',  'Client\ClientPostCommentController@deleteClientAllSubComment');
	Route::put('updateClientAllComment',  'Client\ClientPostCommentController@updateClientAllComment');
	Route::delete('deleteClientAllComment',  'Client\ClientPostCommentController@deleteClientAllComment');

	Route::post('clientLikePost', 'Client\Front\ClientOnlineCourseFrontController@likePost');
	Route::post('clientLikeComment', 'Client\Front\ClientOnlineCourseFrontController@clientLikeComment');
	Route::post('clientLikeSubComment', 'Client\Front\ClientOnlineCourseFrontController@clientLikeSubComment');

	// manage assignment subject
	Route::get('manageAssignmentSubject', 'Client\ClientAssignmentSubjectController@show');
	Route::get('createAssignmentSubject', 'Client\ClientAssignmentSubjectController@create');
	Route::post('createAssignmentSubject', 'Client\ClientAssignmentSubjectController@store');
	Route::get('assignmentSubject/{id}/edit', 'Client\ClientAssignmentSubjectController@edit');
	Route::put('updateAssignmentSubject', 'Client\ClientAssignmentSubjectController@update');
	Route::post('getAssignmentSubjectsByCourse', 'Client\ClientAssignmentSubjectController@getAssignmentSubjectsByCourse');
	Route::delete('deleteAssignmentSubject', 'Client\ClientAssignmentSubjectController@delete');
	Route::post('getAssignmentSubjectsByBatchId', 'Client\ClientAssignmentSubjectController@getAssignmentSubjectsByBatchId');

	// manage assignment topic
	Route::get('manageAssignmentTopic', 'Client\ClientAssignmentTopicController@show');
	Route::get('createAssignmentTopic', 'Client\ClientAssignmentTopicController@create');
	Route::post('createAssignmentTopic', 'Client\ClientAssignmentTopicController@store');
	Route::get('assignmentTopic/{id}/edit', 'Client\ClientAssignmentTopicController@edit');
	Route::put('updateAssignmentTopic', 'Client\ClientAssignmentTopicController@update');
	Route::post('getAssignmentTopicsBySubject', 'Client\ClientAssignmentTopicController@getAssignmentTopicsBySubject');
	Route::delete('deleteAssignmentTopic', 'Client\ClientAssignmentTopicController@delete');


	// manage assignment
	Route::get('manageAssignment', 'Client\ClientAssignmentController@show');
	Route::get('createAssignment', 'Client\ClientAssignmentController@create');
	Route::post('createAssignment', 'Client\ClientAssignmentController@store');
	Route::get('assignment/{id}/edit', 'Client\ClientAssignmentController@edit');
	Route::put('updateAssignment', 'Client\ClientAssignmentController@update');
	Route::post('checkAssignmentExist', 'Client\ClientAssignmentController@checkAssignmentExist');
	Route::delete('deleteAssignment', 'Client\ClientAssignmentController@delete');


	Route::get('studentsAssignment', 'Client\ClientAssignmentController@studentsAssignment');
	Route::post('searchStudentForAssignment', 'Client\ClientAssignmentController@searchStudentForAssignment');
	Route::post('getAssignmentByTopicForStudent', 'Client\ClientAssignmentController@getAssignmentByTopicForStudent');
	Route::get('assignmentRemark/{id}/{studentId}', 'Client\ClientAssignmentController@assignmentRemark');
	Route::post('createAssignmentRemark', 'Client\ClientAssignmentController@createAssignmentRemark');

	Route::get('managePayableSubCategory', 'Client\PurchaseSubCategory\PurchaseSubCategoryController@show');
	Route::get('showPayableSubcategory/{id}', 'Client\PurchaseSubCategory\PurchaseSubCategoryController@showPayableSubcategory');
	Route::post('getPayableSubjectsAndPapersBySubcatIdAssociatedWithQuestion', 'Client\PurchaseSubCategory\PurchaseSubCategoryController@getPayableSubjectsAndPapersBySubcatIdAssociatedWithQuestion');
	Route::post('purchasePayableSubCategory', 'Client\PurchaseSubCategory\PurchaseSubCategoryController@purchasePayableSubCategory');
	Route::get('thankyouPayable', 'Client\PurchaseSubCategory\PurchaseSubCategoryController@thankyouPayable');
	Route::post('webhookPayable', 'Client\PurchaseSubCategory\PurchaseSubCategoryController@webhookPayable');
	Route::post('updatePayableSubCategory', 'Client\PurchaseSubCategory\PurchaseSubCategoryController@updatePayableSubCategory');
	Route::get('managePurchasedSubCategory', 'Client\PurchaseSubCategory\PurchaseSubCategoryController@managePurchasedSubCategory');
	Route::get('showPurchaseSubcategory/{id}', 'Client\PurchaseSubCategory\PurchaseSubCategoryController@showPurchaseSubcategory');

	//chat
	Route::post('clientPrivateChat', 'Client\ClientChatController@clientPrivateChat');
	Route::post('readClientChatMessages', 'Client\ClientChatController@readClientChatMessages');
	Route::post('sendMessage', 'Client\ClientChatController@sendMessage');
	Route::post('showClientChatUsers', 'Client\ClientChatController@showClientChatUsers');
	Route::post('loadClientChatUsers', 'Client\ClientChatController@loadClientChatUsers');
	Route::post('checkOnlineUsers', 'Client\ClientChatController@checkOnlineUsers');
	Route::post('checkDashboardOnlineUsers', 'Client\ClientChatController@checkOnlineUsers');

	// Batch & Attendance
	Route::get('manageBatch', 'Client\ClientBatchController@show');
	Route::get('createBatch', 'Client\ClientBatchController@create');
	Route::post('createBatch', 'Client\ClientBatchController@store');
	Route::get('batch/{id}/edit', 'Client\ClientBatchController@edit');
	Route::put('updateBatch', 'Client\ClientBatchController@update');
	Route::delete('deleteBatch', 'Client\ClientBatchController@delete');
	Route::get('associateBatchStudents', 'Client\ClientBatchController@showBatchStudents');
	Route::post('associateBatchStudents', 'Client\ClientBatchController@associateBatchStudents');
	Route::post('getBatchStudentsIdsbyBatchId', 'Client\ClientBatchController@getBatchStudentsIdsbyBatchId');
	Route::post('searchClientStudent', 'Client\ClientBatchController@searchClientStudent');
	Route::get('manageAttendance', 'Client\ClientBatchController@showAttendance');
	Route::post('getBatchStudentAttendanceByBatchId', 'Client\ClientBatchController@getBatchStudentAttendanceByBatchId');
	Route::post('markAttendance', 'Client\ClientBatchController@markAttendance');
	Route::get('manageAttendanceCalendar', 'Client\ClientBatchController@showAttendanceCalendar');
	Route::post('getBatchUsersByBatchId', 'Client\ClientBatchController@getBatchUsersByBatchId');
	Route::post('getBatchStudentsByBatchId', 'Client\ClientBatchController@getBatchStudentsByBatchId');

	// Offline Paper
	Route::get('manageOfflinePaper', 'Client\ClientOfflinePaperController@show');
	Route::get('createOfflinePaper', 'Client\ClientOfflinePaperController@create');
	Route::post('createOfflinePaper', 'Client\ClientOfflinePaperController@store');
	Route::get('offlinePaper/{id}/edit', 'Client\ClientOfflinePaperController@edit');
	Route::put('updateOfflinePaper', 'Client\ClientOfflinePaperController@update');
	Route::delete('deleteOfflinePaper', 'Client\ClientOfflinePaperController@delete');
	Route::post('getOfflinePapersByBatchId', 'Client\ClientOfflinePaperController@getOfflinePapersByBatchId');
	Route::get('manageOfflineExam', 'Client\ClientOfflinePaperController@manageOfflineExam');
	Route::post('getBatchStudentsAndMarksByBatchIdByPaperId', 'Client\ClientOfflinePaperController@getBatchStudentsAndMarksByBatchIdByPaperId');
	Route::post('assignOfflinePaperMarks', 'Client\ClientOfflinePaperController@assignOfflinePaperMarks');

	// client message
	Route::get('manageMessage', 'Client\ClientMessageController@show');
	Route::get('createMessage', 'Client\ClientMessageController@create');
	Route::post('createMessage', 'Client\ClientMessageController@store');
	Route::get('message/{id}/edit', 'Client\ClientMessageController@edit');
	Route::put('updateMessage', 'Client\ClientMessageController@update');
	Route::delete('deleteMessage', 'Client\ClientMessageController@delete');

	// client offline payment
	Route::get('manageOfflinePayments', 'Client\ClientOfflinePaymentController@show');
	Route::get('createOfflinePayment', 'Client\ClientOfflinePaymentController@create');
	Route::post('createOfflinePayment', 'Client\ClientOfflinePaymentController@store');
	Route::get('offlinePayment/{id}/edit', 'Client\ClientOfflinePaymentController@edit');
	Route::put('updateOfflinePayment', 'Client\ClientOfflinePaymentController@update');
	Route::delete('deleteOfflinePayment', 'Client\ClientOfflinePaymentController@delete');
	Route::get('batchPayments', 'Client\ClientOfflinePaymentController@batchPayments');
	Route::post('getTotalPaidByBatchIdByUserId', 'Client\ClientOfflinePaymentController@getTotalPaidByBatchIdByUserId');
	Route::get('duePayments', 'Client\ClientOfflinePaymentController@duePayments');
	Route::post('getDueStudentsByBatchIdByDueDate', 'Client\ClientOfflinePaymentController@getDueStudentsByBatchIdByDueDate');
	Route::get('userUploadedTransactions', 'Client\ClientOfflinePaymentController@userUploadedTransactions');

	// client teachers
	Route::get('addTeachers', 'Client\ClientTeacherController@addTeacher');
	Route::post('addEmailTeachers', 'Client\ClientTeacherController@addEmailTeacher');
	Route::post('addMobileTeacher', 'Client\ClientTeacherController@addMobileTeacher');
	Route::post('uploadClientTeachers', 'Client\ClientTeacherController@uploadClientTeachers');
	Route::get('allTeachers', 'Client\ClientTeacherController@allTeacher');
	Route::post('changeClientTeacherModuleStatus', 'Client\ClientTeacherController@changeClientTeacherModuleStatus');
	Route::delete('deleteClientTeacher', 'Client\ClientTeacherController@deleteClientTeacher');

	// client class
	Route::get('manageClasses', 'Client\ClientClassController@show');
	Route::get('createClientClass', 'Client\ClientClassController@create');
	Route::post('createClientClass', 'Client\ClientClassController@store');
	Route::get('class/{id}/edit', 'Client\ClientClassController@edit');
	Route::put('updateClientClass', 'Client\ClientClassController@update');
	Route::delete('deleteClass', 'Client\ClientClassController@delete');
	Route::get('manageSchedules', 'Client\ClientClassController@manageSchedules');


	// client exam
	Route::get('manageExams', 'Client\ClientExamController@show');
	Route::get('createClientExam', 'Client\ClientExamController@create');
	Route::post('createClientExam', 'Client\ClientExamController@store');
	Route::get('exam/{id}/edit', 'Client\ClientExamController@edit');
	Route::put('updateClientExam', 'Client\ClientExamController@update');
	Route::delete('deleteExam', 'Client\ClientExamController@delete');

	// client holiday
	Route::get('manageHolidays', 'Client\ClientHolidayController@show');
	Route::get('createClientHoliday', 'Client\ClientHolidayController@create');
	Route::post('createClientHoliday', 'Client\ClientHolidayController@store');
	Route::get('holiday/{id}/edit', 'Client\ClientHolidayController@edit');
	Route::put('updateClientHoliday', 'Client\ClientHolidayController@update');
	Route::delete('deleteHoliday', 'Client\ClientHolidayController@delete');

	// client notice
	Route::get('manageNotices', 'Client\ClientNoticeController@show');
	Route::get('createClientNotice', 'Client\ClientNoticeController@create');
	Route::post('createClientNotice', 'Client\ClientNoticeController@store');
	Route::get('notice/{id}/edit', 'Client\ClientNoticeController@edit');
	Route::put('updateClientNotice', 'Client\ClientNoticeController@update');
	Route::delete('deleteNotice', 'Client\ClientNoticeController@delete');

	// individual message
	Route::get('manageIndividualMessage', 'Client\ClientIndividualMessageController@show');
	Route::get('createIndividualMessage', 'Client\ClientIndividualMessageController@create');
	Route::post('createIndividualMessage', 'Client\ClientIndividualMessageController@store');
	Route::get('individualMessage/{id}/edit', 'Client\ClientIndividualMessageController@edit');
	Route::delete('deleteIndividualMessage', 'Client\ClientIndividualMessageController@delete');
	Route::post('getIndividualMessagesByDate', 'Client\ClientIndividualMessageController@getIndividualMessagesByDate');

});