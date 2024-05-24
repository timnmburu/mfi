<?php

?>

<button hidden type="button" class="btn btn-primary" id="liveToastBtn">Show live toast</button>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="liveToast" class="toast bg-dark text-light" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header bg-dark text-light">
      <strong class="me-auto">Essentialapp</strong>
      <small>Now</small>
      <button type="button" class="btn-close bg-light" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body" >
        <span id="displayMsgBox" value="" ></span>
    </div>
  </div>
</div>

<script>
//Open the toaster
    const toastTrigger = document.getElementById('liveToastBtn');
    const toastLiveExample = document.getElementById('liveToast');
    
    if (toastTrigger) {
        const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample);
        toastTrigger.addEventListener('click', () => {
            toastBootstrap.show()
        });
    }

    let toasterMsg = localStorage.getItem('toasterMessage'); 
    
    document.getElementById('displayMsgBox').innerText = toasterMsg;
</script>