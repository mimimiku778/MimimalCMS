<main>
    <hr>
    <section>
        <form action="/image/store" method="POST" enctype="multipart/form-data">
            <label for="file">Choose a file:</label>
            <input type="file" name="file" id="file">
            <label>Image Type:</label>
            <input type="radio" name="imageType" id="image-WEBP" value="WEBP" checked>
            <label for="image-WEBP">WEBP</label>
            <input type="radio" name="imageType" id="image-JPG" value="JPG">
            <label for="image-JPG">JPG</label>
            <input type="radio" name="imageType" id="image-PNG" value="PNG">
            <label for="image-PNG">PNG</label>
            <br>
            <label>Image Size:</label>
            <input type="radio" name="imageSize" id="image-size-640" value="640" checked>
            <label for="image-size-640">Up to 640px</label>
            <input type="radio" name="imageSize" id="image-size-1000" value="1000">
            <label for="image-size-1000">Up to 1000px</label>
            <input type="radio" name="imageSize" id="image-size-max" value="0">
            <label for="image-size-max">Max size</label>
            <br>
            <input type="submit" value="Upload">
        </form>
    </section>
    <?php if (session()->hasError()) : ?>
        <header>
            <?php foreach (session()->getError() as $key => $error) : ?>
                <p><?php echo $error['code'] . ': ' . $error['message'] ?></p>
            <?php endforeach ?>
        </header>
    <?php endif ?>
    <?php if (session()->has('image')) : ?>
        <hr>
        <section>
            <figure>
                <img style="display:block" src="<?php echo url('images/') . session('image') ?>">
                <figcaption>
                    <i><small><?php echo '/images/' . session('image') ?></small></i>
                </figcaption>
            </figure>
        </section>
    <?php endif ?>
</main>