<style>
    /* Scroll to Top button */
    #scrollToTopBtn {
        display: none;
        position: fixed;
        bottom: 50px;
        right: 20px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: #555;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
        zoom:75%;
    }
    
    #scrollToTopBtn:hover {
        background-color: black;
    }
</style>

<!--Scroll to Top button-->
<button class="border rounded-pill" onclick="topFunction()" id="scrollToTopBtn" title="Go to top"><i class="bi bi-chevron-bar-up "></i>Top</button>

<script>
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};
    
    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("scrollToTopBtn").style.display = "block";
        } else {
            document.getElementById("scrollToTopBtn").style.display = "none";
        }
    }
    
    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
    
</script>