<?php
//echo "<pre>"; print_r($sessions); exit("</pre>");
?>

<style>
    .session-name-item{
        cursor: pointer;
    }
    .session-name-item:hover{
        background-color: #ececec;
    }

    .users-list-item{
        cursor: pointer;
    }
    .users-list-item:hover{
        background-color: #3F474E;
    }

    .chat-body{
        border: 1px solid;
        height: 400px;
        overflow: scroll;
    }

    .admin-to-user-text-admin{
        color: white;
        background-color: #df941f;
        display: list-item;
        list-style: none;
        margin-top: 7px;
        margin-bottom: 7px;
        padding-right: 12px;
        border-radius: 15px;
        text-align: right;
        margin-left: 50px;
        margin-right: 10px;
    }

    .user-to-admin-text-admin{
        color: white;
        background-color: #1f8edf;
        display: list-item;
        list-style: none;
        margin-top: 7px;
        margin-bottom: 7px;
        padding-left: 10px;
        border-radius: 15px;
        margin-left: 5px;
        margin-right: 75px;
    }

    /******* Toggle Switch ********/
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #d24040;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #579B58;
    }

    input:focus + .slider {
        box-shadow: 0 0 10px red;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    .users-list-body{
        height: 451px;
        overflow: scroll;
    }

</style>


<div class="content-wrapper" >
    <div class="container" id="container">
        <!-- start: PAGE TITLE -->
        <section id="page-title" style="padding: 15px 0;">
            <div class="row">
                <div class="col-md-10">
                    <h1 class="mainTitle">
                        Live Support Chat
                    </h1>
                </div>
                <div class="col-md-2 text-right">
                    <label class="switch">
                        <input id="liveSupportChatToggle" type="checkbox" <?=($status)?'checked':''?>>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </section>

        <!-- start: PAGE CONTENTS -->
        <div class="col-md-12" style="margin-top: 10px;">
            <div class="card card-default">
                <div class="card-body">
					<div class="row">
						<div class="col-md-4">
							<div class="card card">
								<div class="card-heading text-center">
									<h3 class="attendees-list-title text-center">Users</h3>
								</div>
								<div class="card-body users-list-body">
									<ul class="users-list list-group">
										<!-- Will be filled by listAllUsers() method -->
										<li class="users-list-item list-group-item ">Loading...</li>
										<img src="<?=$this->project_url?>front_assets/support_chat/ycl_anime_500kb.gif">
									</ul>
								</div>
								<div class="card-footer">
								</div>
							</div>
						</div>

						<div class="col-md-8">
							<div class="card card-default">
								<div class="card-heading">
									<h3 class="chat-user-name card-title ml-3"></h3>
								</div>
								<div class="card-body">
									<div class="chat-body" session-id="" user-id="" user-name="">

									</div>
									<div class="input-group text-center" style="width: 100%; position: absolute; bottom: 60px;">
										<span id="currentUserTypingHint" style="display: none;"></span>
									</div>
									<div class="input-group" style="margin-top: 30px">
										<input id="message-text-input" type="text" class="form-control" placeholder="Enter message">
										<span class="input-group-btn">
											<button class="send-text-btn btn btn-success" type="button" user-id="" session-id=""><i class="far fa-paper-plane"></i> Send</button>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>


    </div>
</div>
</div>
<!-- end: FEATURED BOX LINKS -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@9.17.0/dist/sweetalert2.all.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous"></script>

<script>
    let live_support_chat_room = project_name+project_id+"_live_support";
    let base_url = "<?=$this->project_url?>/";

    let admin_id = "<?=$this->session->userdata('aid')?>";

    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };


    $(document).ready(function () {

        listAllUsers();

        // If clicked on any user
        $('.users-list').on('click', '.users-list-item', function () {

            $(this).children('.badge').hide();

            let user_id = $(this).attr('user-id');
            let user_name = $(this).attr('user-name');

            $('.chat-user-name').text(user_name);
            $('.send-text-btn').attr('user-id', user_id);
            $('.chat-body').attr('user-id', user_id);
            $('.chat-body').attr('user-name', user_name);

            $.get(project_url+"/admin/Live_support_chat/allChatsJSON/"+user_id)
            .done(function(chats) {

                    // $.get(base_url+"admin/sessions/markAllAsRead/"+session_id+'/'+user_id, function( data ) {
                    //     if (data == 1)
                    //     {
                    //
                    //     }
                    // });

                    chats = JSON.parse(chats);

                    $('.chat-body').html('');
                    $.each(chats, function(index, chat)
                    {
                        if (chat.chat_from_type == 'admin'){
                            $('.chat-body').append('' +
                                '<span class="admin-to-user-text-admin">'+chat.text+'</span>');
                        }else{
                            $('.chat-body').append('' +
                                '<span class="user-to-admin-text-admin"><strong style="margin-right: 10px">'+user_name+'</strong>'+chat.text+'</span>');
                        }
                    });

                    $(".chat-body").scrollTop($(".chat-body")[0].scrollHeight);

                }
            ).error((error)=>{
                getTranslatedSelectAccess('Unable to load the chat.').then((msg) => {
                    toastr.error(msg);
                });
            }
            ).fail(()=>{
                getTranslatedSelectAccess('Unable to load the chat.').then((msg) => {
                    toastr.error(msg);
                });
            });

        });

        // When send button is clicked
        $('.send-text-btn').on('click', function () {
            let userId = $(this).attr('user-id');
            let message = $('#message-text-input').val();
			console.log(userId);
            if (userId == '')
            {
                getTranslatedSelectAccess('You should choose an attendee to send the text.').then((msg) => {
                    toastr.error(msg);
                });
                return false;
            }

            if (message == '')
            {
                getTranslatedSelectAccess('Please enter some message.').then((msg) => {
                    toastr.error(msg);
                });
                return false;
            }

            $.post(project_url+"/admin/Live_support_chat/newText",

                {
                    to_id: userId,
                    chat_text: message
                }

            ).done(function(response)
                {
                    try { $.parseJSON(response);}
                    catch(error) { 
                        getTranslatedSelectAccess('You are not logged-in').then((msg) => {
							toastr.error(msg);
						});
                        return false; 
                    }

                    response = JSON.parse(response);
                    if (response.status == 'success')
                    {
                        socket.emit('newLiveSupportText', {'room':live_support_chat_room, 'text':message, 'fromType':'admin', 'fromName':'', 'fromId':admin_id, 'to_id':userId});

                        $('.chat-body').append('' +
                            '<span class="admin-to-user-text-admin">'+message+'</span>');

                        $('#message-text-input').val('');

                        $(".chat-body").scrollTop($(".chat-body")[0].scrollHeight);

                    }else{
                        getTranslatedSelectAccess('User updated').then((msg) => {
							toastr.error(msg);
						});
                    }
                }
            ).error((error)=>{
                getTranslatedSelectAccess('Unable to send the text.').then((msg) => {
                    toastr.error(msg);
                });
            });


        });

        $('#message-text-input').keydown(function (e){
            if(e.keyCode == 13 || e.key === 'Enter') // Send text
            {
                $('.send-text-btn').click();

            }else{ // Admin typing something
                let userId = $('.send-text-btn').attr('user-id');
                socket.emit('typingLiveSupportText', {'room':live_support_chat_room, 'fromType':'admin', 'fromName':'', 'fromId':admin_id, 'to_id':userId});
            }
        });



        /**************** Real-time data **********************/
        socket.on("typingLiveSupportText", function (data){
            if (data.room == live_support_chat_room && data.fromType == "attendee")
            {
                if ($('.send-text-btn').attr('user-id') == data.fromId)
                {
                    $('#currentUserTypingHint').text(data.fromName+' is typing...');
                    $('#currentUserTypingHint').show();
                    setTimeout(function () {
                        $('#currentUserTypingHint').hide();
                    }, 1000);

                }else{ // Show typing hint for other user

                    $('.users-list li[user-id="' + data.fromId + '"] > .typingHint').show();
                    setTimeout(function () {
                        $('.users-list li[user-id="' + data.fromId + '"] > .typingHint').hide();
                    }, 1000);
                }
            }
        });

        socket.on("newLiveSupportText", function (data){
            if (data.room == live_support_chat_room && data.fromType == "attendee")
            {
                $('.users-list li[user-id="' + data.fromId + '"]').attr('new-text', 1);
                $('.users-list li[user-id="' + data.fromId + '"] > .badge').show();
                $(".users-list li").sort(newtext_dec_sort).appendTo('.users-list');

                if ($('.send-text-btn').attr('user-id') == data.fromId)
                {
                    $('.chat-body').append('' +
                        '<span class="user-to-admin-text-admin"><strong style="margin-right: 10px">'+data.fromName+'</strong>'+data.text+'</span>');
                    $(".chat-body").scrollTop($(".chat-body")[0].scrollHeight);

                }else{ // Show notification

                }
            }
        });

        socket.on("endLiveSupportChat", function (data){ // Attendee ended the chat
            if (data.room == live_support_chat_room)
            {
                // data.id is the user ID
            }
        });

        socket.on("userLeftLiveSupportChat", function (data){ // Attendee left the chat
            if (data.room == live_support_chat_room)
            {
                // data.id is the user ID
            }
        });
        /************* End of real-time data ******************/




        /**************** Status change **********************/
        $('#liveSupportChatToggle').on('change', function () {
            const translationData = fetchAllText(); // Fetch the translation data

            translationData.then((arrData) => {
                const selectedLanguage = $('#languageSelect').val(); // Get the selected language

                // Find the translations for the dialog text
                let dialogTitle = 'Please Wait';
                let dialogText = 'We are changing the status';
			    let imageAltText = 'Loading...';

                // Toast
                let loggedText= "You are not logged-in";
                let liveText = "Live support status changed";

                // Swal 2
                let errorText2 = "Error";
                let errorMsg2 = "Unable to change the status";

                for (let i = 0; i < arrData.length; i++) {
                    if (arrData[i].english_text === dialogTitle) {
                        dialogTitle = arrData[i][selectedLanguage + '_text'];
                    }
                    if (arrData[i].english_text === dialogText) {
                        dialogText = arrData[i][selectedLanguage + '_text'];
                    }
                    if (arrData[i].english_text === imageAltText) {
                        imageAltText = arrData[i][selectedLanguage + '_text'];
                    }

                    if (arrData[i].english_text === loggedText) {
                        loggedText = arrData[i][selectedLanguage + '_text'];
                    }

                    if (arrData[i].english_text === errorText2) {
                        errorText2 = arrData[i][selectedLanguage + '_text'];
                    }
                    if (arrData[i].english_text === errorMsg2) {
                        errorMsg2 = arrData[i][selectedLanguage + '_text'];
                    }
                }

                Swal.fire({
                    title: dialogTitle,
				    text: dialogText,
                    imageUrl: project_url+'front_assets/support_chat/ycl_anime_500kb.gif',
				    imageAlt: imageAltText,
                    showCancelButton: false,
                    showConfirmButton: false,
                    allowOutsideClick: false
                });

                let status = 0;
                if ($(this).is(":checked"))
                    status = 1;

                $.get(project_url+"/admin/live_support_chat/toggleStatus/"+status, function (response) {

                    try { $.parseJSON(response);}
                    catch(error)
                    {
                        $('#liveSupportChatToggle').prop("checked", !$('#liveSupportChatToggle').prop("checked"));
                        toastr.error(loggedText);
                        return false;
                    }

                    response = JSON.parse(response);
                    if (response.status == 'failed')
                    {
                        Swal.fire(
                            errorText2,
                            errorMsg2,
                            'error'
                        );
                        $('#liveSupportChatToggle').prop("checked", !$('#liveSupportChatToggle').prop("checked"));
                    }else{
                        socket.emit("supportChatStatusChange", {'room':live_support_chat_room, 'status':status});
                        Swal.close();
                        toastr.info(liveText);
                    }

                }).fail(()=>{
                    Swal.fire(
                        errorText2,
                        errorMsg2,
                        'error'
                    );
                    $('#liveSupportChatToggle').prop("checked", !$('#liveSupportChatToggle').prop("checked"));
                });
            });
        });
        /****************************** End of status change ******************************/

    });


    function listAllUsers()
    {
        $.get(project_url+"/admin/users/allUsersJson/", function (users) {
			console.log(users);

            $('.users-list').html('');
            users = JSON.parse(users);
            $.each(users, function(i, user) {
                $('.users-list').append(
                    '<li class="users-list-item list-group-item w-100" user-id="'+user.id+'" user-name="'+user.name+' '+user.surname+'" new-text="0">'+user.name+' '+user.surname+'<img class="typingHint" src="'+ycl_root+'/theme_assets/live_support/typing-animation-3x.gif" style="margin-left: 20px;width: 30px;display: none;"> <span class="badge badge-danger ml-2" style="display: none;">new</span></li>'
                );
            });

        }).fail(()=>{

            const translationData = fetchAllText(); // Fetch the translation data

            translationData.then((arrData) => {
                const selectedLanguage = $('#languageSelect').val(); // Get the selected language

                // Find the translations for the dialog text
                let errorText = 'Error';
                let errorMsg = 'Unable to load users list';

                for (let i = 0; i < arrData.length; i++) {
                    if (arrData[i].english_text === errorText) {
                        errorText = arrData[i][selectedLanguage + '_text'];
                    }
                    if (arrData[i].english_text === errorMsg) {
                        errorMsg = arrData[i][selectedLanguage + '_text'];
                    }
                }
                Swal.fire(
                    errorText,
                    errorMsg,
                    'error'
                );
                
            });
        });
    }

    function newtext_asc_sort(a, b) {
        return ($(b).attr('new-text')) < ($(a).attr('new-text')) ? 1 : -1;
    }
    function newtext_dec_sort(a, b) {
        return ($(b).attr('new-text')) > ($(a).attr('new-text')) ? 1 : -1;
    }
</script>
