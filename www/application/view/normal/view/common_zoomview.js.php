<script type="text/javascript">
var zoomIntensity = 0.2;

var holder = document.getElementById("holder"),
    context = holder.getContext("2d"),
    hold_w = null, hold_h = null;

var scale = 1,
    hold_x = 0, hold_y = 0,
    hold_vw = null, hold_vh = null;

var img = new Image(),
    loadedImage = false,
    img_x = null, img_y = null;
    img_w = null, img_h = null;

var down_x = null, down_y = null,
    down_ix = null, down_iy = null;

img.addEventListener("load", function() {
    loadedImage = true;
}, false);
img.src = '<?php p($mImage); ?>';

function draw(){
    if (hold_w != null && loadedImage) {
        // Clear screen to white.
        if (img_w === null) {
            img_y = -1; // temp
            if (img.width > hold_w) {
                img_w = hold_w;
                img_h = img.height / img.width * hold_w;

                img_x = 0;
                img_y = (hold_h - img_h) / 2;
            }
            if (img.height > hold_h && img_y < 0) {
                img_w = img.width / img.height * hold_h;
                img_h = hold_h;

                img_x = (hold_w - img_w) / 2;
                img_y = 0;
            }
            else {
                img_w = img.width;
                img_h = img.height;
                img_x = (hold_w - img_w) / 2; 
                img_y = (hold_h - img_h) / 2;  
            }
        }

        context.fillStyle = "white";
        context.fillRect(hold_x, hold_y, hold_vw, hold_vh);
        context.drawImage(img, img_x, img_y, img_w, img_h);
    }
}
// Draw loop at 60FPS.
setInterval(draw, 1000/60);

zoomImage = function(cx, cy, zoom) {
    context.translate(hold_x, hold_y);
  
    hold_x -= cx/(scale*zoom) - cx/scale;
    hold_y -= cy/(scale*zoom) - cy/scale;
    
    context.scale(zoom, zoom);
    context.translate(-hold_x, -hold_y);

    scale *= zoom;
    hold_vw = hold_w / scale;
    hold_vh = hold_h / scale;
}

holder.onmousewheel = function (event){
    event.preventDefault();
    // Get mouse offset.
    var mousex = event.clientX - holder.offsetLeft;
    var mousey = event.clientY - holder.offsetTop;

    var wheel = event.wheelDelta/120;

    var zoom = Math.exp(wheel*zoomIntensity);
    
    zoomImage(mousex, mousey, zoom);
}

holder.onmousedown = function (event) {
    down_x = event.clientX;
    down_y = event.clientY;
    down_ix = img_x;
    down_iy = img_y;
}

holder.onmousemove = function (event) {
    if (down_x !== null) {
        dx = (event.clientX - down_x) / scale;
        dy = (event.clientY - down_y) / scale;
        img_x = down_ix + dx;
        img_y = down_iy + dy;
    }
}

holder.onmouseup = function (event) {
    down_x = null;
    down_y = null;
}

holder.onmouseout = function (event) {
    down_x = null;
    down_y = null;
}

$(function() {
    $(window).resize(function(){
        w = $(window).width() - 40; 
        holder.width = w; $('#holder').width(w);

        h = $(window).height() - 40;
        holder.height = h; $('#holder').height(h);

        hold_w = w; hold_h = h;
        hold_vw = hold_w / scale;
        hold_vh = hold_h / scale;
    });

    $('#zoom_in').click(function() {
        zoomImage(hold_w / 2, hold_h / 2, 1.2);
    });

    $('#zoom_out').click(function() {
        zoomImage(hold_w / 2, hold_h / 2, 1 / 1.2);
    });
});
</script>