<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<meta http-equiv='X-UA-Compatible' content='IE=edge'>
<title>Page Title</title>
<meta name='viewport' content='width=device-width, initial-scale=1'>

<link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.css" />
<link rel="stylesheet" href="/kko/css/test.css"/>
</head>
<body>
<h1> TOAST UI Editor</h1>

<div id="editor"></div>

<button id="btn" class="w-btn w-btn-indigo" type="button">등록</button>

<div id="viewer"></div>
<script type="text/javascript" src="/js/jquery-3.5.1.min.js"></script>
<script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>
<script>

const content = [].join('\n');
const editor = new toastui.Editor({
    el: document.querySelector('#editor'),
    previewStyle: 'vertical',
    height: '500px',
    initialValue: content
});

const viewer = toastui.Editor.factory({
    el: document.querySelector('#viewer'),
    viewer: true,
    height: '500px',
    initialValue: content
});

$('#btn').click(function(){
    let ed = editor.getMarkdown();
    
    viewer.setMarkdown(ed);
    console.log(ed);
    $.ajax(
    {
        type: 'POST',
        url: '/kko/ax.php?mode=1',
        data : {text :ed},
        dataType: 'text',
        success: function(data) {
            console.log(data);
            viewer.setMarkdown(data);        
        }
    });
});

</script>
</body>
</html>

