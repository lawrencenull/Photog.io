<?PHP echo form_open_multipart('upload'); ?>
<div id="holder"><p>Drag photo here or click to browse</p></div>
<!-- <img src="" alt="Preview of your photo to be uploaded" class="upload-preview"> -->

<progress id="uploadprogress" max="100" value="0">0</progress>

<label for="file">File</label><input type="file" name="file" id="file" accept="image/*" />
<label for="caption">Caption</label><textarea name="caption" id="caption"><?PHP echo set_value('caption'); ?></textarea>

<button class="btn btn-primary btn-large" type="submit" name="submit">Upload</button>

<input type="hidden" name="photo-id" value=""  />
<?PHP echo form_close(); ?>