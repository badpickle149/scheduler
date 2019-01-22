(function() {

  window.onload = function() {
    assignDelete();
  }

  function assignDelete() {
    let rows = document.querySelectorAll('tbody > tr');
    for (let i = 0; i < rows.length; i++) {
      rows[i].onclick = deleteRow;
    }
  }


  function deleteRow() {
    let name = this.children;
    let task = "";
    for (let i = 0; i < name.length; i++) {
      if (name[i].classList.contains("task")) {
        task = name[i].innerText;
      }
    }
    deleteData(task);
  }

  function checkStatus(response) {
    if (response.status >= 200 && response.status < 300) {
        return response.text();
    } else {
        return Promise.reject(new Error(response.status +
                                        ": " + response.statusText));
    }
  }

  function deleteData(task) {
    let url = "delete.php?task=" + task;
    fetch(url, {credentials: 'include'})
      .then(checkStatus)
      .then(JSON.parse)
      .then(respond)
      .catch(alert)
  }

  function respond(response) {
    location.reload();
    let msg = document.createElement('span');
    msg.innerText = response.msg;
    let hdr = document.querySelector('header');
    hdr.appendChild(msg);
  }

})();
