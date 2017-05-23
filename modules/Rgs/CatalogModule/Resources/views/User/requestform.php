{* file:[RgsCatalogModule]User/portal.tpl *}

{extends file='file:rgs_layout.php'}

{block name="carousel"}
{include file='file:includes/noCarousel.tpl'}
{/block}

{block  name=section}

<h1>Send a request</h1>

<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, 
sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. 
Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. 
Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

{form var="user_request" method="post" data-toggle="validator" novalidate="true" enctype="multipart/form-data" class="col-sm-6 col-sm-offset-3"}
    <div class="form-group">
    <label for="subject">{form_error path="subject" style="color: red;"}</label>
    {form_input path="subject" class="form-control" placeholder="Subject"}
    {/form_input}
    </div>
    <div class="form-group">
    <label for="subject">{form_error path="description" style="color: red;"}</label>
    {form_textarea path="description" class="form-control" placeholder="Description"}
    {/form_textarea}
    </div>
    <div class="form-group">
    {form_input type="file" path="image" class="form-control" placeholder="Image" accept=".png,.jpg,.jpeg,.bmp"}
    {/form_input}
    </div>
    <div class="form-group">
    {form_submit class="form-control btn-success"}
    </div>
{/form}

{/block}