/**
 * Created by longzhubaby on 2014/10/23.
 */
$(document).ready(function(){
    $(function(){
        $("#ch_email").focus(function(){
            $("#ch_emailTips").text("");
        });
        $("#ch_email").blur(function(){
           ch_checkEmail();
        });

        $("#ch_oldpass").focus(function(){
            $("#ch_oldpassTips").text("");
        });
        $("#ch_oldpass").blur(function(){
           ch_checkOldPass();
        });
 

        $("#ch_pass1").focus(function(){
            $("#ch_pass1Tips").text("");
        });
        $("#ch_pass1").blur(function(){
            ch_checkPass1();
        });
        $("#ch_pass2").focus(function(){
            $("#ch_pass2Tips").text("");
        });
        $("#ch_pass2").blur(function(){
            ch_checkPass2();
        });

        $("#ch_oldseePass1").click(function()
        {
            if($(this).attr("class")=="glyphicon glyphicon-eye-close")
            {
                $(this).attr("class","glyphicon glyphicon-eye-open");
                $("#ch_oldpass1-1").val($("#ch_oldpass").val());
                $("#ch_oldpass").hide();
                $("#ch_oldpass1-1").show();
            }
            else{
                $(this).attr("class","glyphicon glyphicon-eye-close");
                $("#ch_oldpass").val($("#ch_oldpass1-1").val());
                $("#ch_oldpass").show();
                $("#ch_oldpass1-1").hide();
            }
        });


        $("#ch_seePass1").click(function()
        {
            if($(this).attr("class")=="glyphicon glyphicon-eye-close")
            {
                $(this).attr("class","glyphicon glyphicon-eye-open");
                $("#ch_pass1-1").val($("#ch_pass1").val());
                $("#ch_pass1").hide();
                $("#ch_pass1-1").show();
            }
            else{
                $(this).attr("class","glyphicon glyphicon-eye-close");
                $("#ch_pass1").val($("#ch_pass1-1").val());
                $("#ch_pass1").show();
                $("#ch_pass1-1").hide();
            }
        });
        $("#ch_seePass2").click(function(){
            if($(this).attr("class")=="glyphicon glyphicon-eye-close")
            {
                $(this).attr("class","glyphicon glyphicon-eye-open");
                $("#ch_pass2-1").val($("#ch_pass2").val());
                $("#ch_pass2").hide();
                $("#ch_pass2-1").show();
            }
            else{
                $(this).attr("class","glyphicon glyphicon-eye-close");
                $("#ch_pass2").val($("#ch_pass2-1").val());
                $("#ch_pass2").show();
                $("#ch_pass2-1").hide();
            }
        });



        $("#ch_changepwdbtn").on("click",function(){
            if(ch_checkOldPass()&&ch_checkEmail()&&ch_checkPass1()&&ch_checkPass2())
            {
                email=$("#ch_email").val();
                ch_olpass=$("#ch_oldpass").val();
                ch_pass1=$("#ch_pass1").val();
                $.post("login.php",
                    {emailp:email,passwordold:ch_olpass,passwordnew:ch_pass1},
                    function(data,status)
                    {
                        if(data=="0")
                    {
                        alert("账号不存在");
                    }else if (data=="1") {
                        alert("旧密码错误");
                    }else
                    {
                        window.location.reload();
                        location.href="login.html";
                    }

                    });

   
            }else
            {
                alert("请完善信息");
            }
        });
    });
});

function ch_checkOldPass()
{
    if($("#ch_oldpass").attr("hidden")=="hidden")
    {
        $("#ch_oldpass").val($("#ch_oldpass1-1").val());
    }
    var pass1=$("#ch_oldpass").val();
    if(pass1.length<6||pass1.length>20)
    {
        $("#ch_oldpassTips").text("*密码长度为"+pass1.length+",不符合要求");
        return false;
    }else{
        return true;
    }
}

function ch_checkEmail()
{
    var email=$("#ch_email").val();
    var regex= /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if(email.length==0)
    {
        $("#ch_emailTips").text("*请输入邮箱账号");
    }
    else if(!regex.test(email))
    {
        $("#ch_emailTips").text("*邮箱格式不符合要求");
        return false;
    }else{
        return true;
    }
}
function ch_checkPass1()
{
    if($("#ch_pass1").attr("hidden")=="hidden")
    {
        $("#ch_pass1").val($("#ch_pass1-1").val());
    }
    var pass1=$("#ch_pass1").val();
    if(pass1.length<6||pass1.length>20)
    {
        $("#ch_pass1Tips").text("*密码长度为"+pass1.length+",不符合要求");
        return false;
    }else{
        return true;
    }
}
function ch_checkPass2()
{
    if($("#ch_pass1").attr("hidden")=="hidden")
    {
        $("#ch_pass1").val($("#ch_pass1-1").val());
    }
    if($("#ch_pass2").attr("hidden")=="hidden")
    {
        $("#ch_pass2").val($("#ch_pass2-1").val());
    }
    var pass1=$("#ch_pass1").val();
    var pass2=$("#ch_pass2").val();
    if(pass1!=pass2)
    {
        $("#ch_pass2Tips").text("*两次输入的密码不相同");
        return false;
    }else{
        return true;
    }
}