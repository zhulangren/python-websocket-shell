<!DOCTYPE html>


<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>配置文件编辑</title>
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="./bootstrap/css/signin.css">
    <script src="./bootstrap/js/jquery-2.1.4.js"></script>
    <script src="./bootstrap/js/modal.js" type="text/javascript"></script>
    <script src="./bootstrap/js/tooltip.js"></script>
    <script src="./bootstrap/js/check_add_shell.js"></script>

    <?php
    	header("Content-Type: text/html;charset=utf-8");
    	require_once('config.php');  
        if(!isset($_SESSION['islogin'])||$_SESSION['islogin']==false)
        {
          header("location: login.html");
        }
  	?>
</head>
<body>
<div class="alert alert-success" role="alert">
</div>

<div class="container">

<table class="table">
    <thead>
    <tr>
        <th>账号</th>
        <th>权限</th>
        <th>编辑</th>
        <th>删除</th>
    </tr>
    </thead>
    <tbody>
    <?php 
        $i=0;$j=0;
        foreach ($accounts  as  $key => $value) {
            $rowstyle="active";
            if($i==1)
            {
                $rowstyle="success";
            }else if($i==2)
            {
                $rowstyle="warning";
            }

        	echo '<tr class="'.$rowstyle.'"> <td id="ac'.$j.'">'.$key.'</td>';
            echo '<td><input type="number"min="0"max="100"step="1" id="ap'.$j.'" value="'.$value->power.'"></td>';
            echo '<td><button class="btn btn-danger btn-xs" id="ae'.$j.'" type="button" onclick ="return onAccSubmit(this.id)">编辑</button></td>';
            echo '<td><button class="btn btn-danger btn-xs" id="ad'.$j.'" type="button" onclick ="return onAccSubmit(this.id)">删除</button></td></tr>';
            $i++;$j++;
            if($i>=3)
            {
                $i=0;
            }
        }
    ?>
    </tbody>
</table>
</div>


<div class="alert alert-info" role="alert">
</div>


<div class="container">

    <table class="table">
        <thead>
        <tr>
            <th>名称</th>
            <th>权限</th>
            <th>ID</th>
            <th>Shell</th>
            <th>编辑</th>
            <th>删除</th>
        </tr>
        </thead>
        <tbody>

        <?php 
            $i=0;$j=0;
            foreach ($fun_list  as  $key => $value) {
                $rowstyle="active";
                if($i==1)
                {
                    $rowstyle="success";
                }else if($i==2)
                {
                    $rowstyle="warning";
                }

                echo '<tr class="'.$rowstyle.'"> <td><input type="text" id="bn'.$j.'" class="form-control" placeholder="name" value="'.$key.'"></td>';
                echo '<td><input type="number"min="0" max="100" step="1" id="bp'.$j.'" value="'.$value->power.'"></td>';
                echo '<td><input type="number"min="0" max="100" step="1" id="bi'.$j.'" value="'.$value->index.'"></td>';
                echo ' <td><input type="text" class="form-control" id="bs'.$j.'" placeholder="Shell Path" value="'.$value->shell.'"></td>';
                echo '<td><button class="btn btn-primary btn-xs" id="be'.$j.'" type="button" onclick ="return onShellSubmit(this.id)">编辑</button></td>';
                echo '<td><button class="btn btn-primary btn-xs" id="bd'.$j.'" type="button" onclick ="return onShellSubmit(this.id)">删除</button></td>';
                $i++;$j++;
                if($i>=3)
                {
                    $i=0;
                }
            }
        ?>

      
        </tbody>
    </table>
</div>
<div class="alert alert-info" role="alert">
    <div class="container"><button class="btn btn-danger"  data-toggle="modal" data-target="#myModal" type="submit">添加Shell</button></div>
</div>





<!-- 模态框（Modal） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal" aria-hidden="false">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    添加命令
                </h4>
            </div>
            <div class="modal-body">
                <form role="form">
                    
                    <div class="form-group">
                        <label for="email">名称</label><span id="emailTips" style="color: red;"></span>
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-tag"></span></span>
                            <input type="input" class="form-control" id="email" placeholder="显示名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="power">权限</label>
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-wrench"></span></span>
                           <input type="number"min="0"max="100"step="1"value="6" id="power">
                           </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="index">ID</label>
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-wrench"></span></span>
                            <input type="number"min="0"max="100"step="1"value="6" id="index">
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="Shell">Shell</label><span id="shellTips" style="color: red;"></span>
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-menu-right"></span></span>
                            <input type="input" class="form-control" id="Shell" placeholder="Shell Path">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info"
                        data-dismiss="modal">取消
                </button>
                <button type="button" id="addshell" class="btn btn-success">
                    确定
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>



</body>
</html>
