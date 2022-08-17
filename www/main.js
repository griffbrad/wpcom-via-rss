function handleClickOn(id, handler) {
    document.body.addEventListener('click', function (e) {
        if (e.target.id !== id) {
            return;
        }

        e.preventDefault();
        handler(e);
    });
}

function xhr(method, params, handler) {
    var request = new XMLHttpRequest(),
        overlay = document.createElement('div');

    overlay.className = 'loading-overlay';
    overlay.innerText = 'Please wait...';

    document.body.appendChild(overlay);

    request.addEventListener('load', function () {
        var responseElement = document.createElement('html');
        responseElement.innerHTML = request.responseText;

        document.getElementById('content').innerHTML = responseElement.querySelector('#content').innerHTML;

        if (handler) {
            handler();
        }

        document.body.removeChild(overlay);
    });

    request.open(method, '', true);
    request.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
    request.send(params ? JSON.stringify(params) : null);
}

