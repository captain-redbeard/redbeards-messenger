            <div class="box-wrapper">
                <form class="box-form" method="POST" action="<?=$data['BASE_HREF'] . '/contacts/edit/' . $data['guid'];?>">
                    <h1>Edit Contact</h1>
                    
                    <input class="glow w100" type="text" name="alias" title="Alias" tabindex="1" placeholder="Alias" value="<?=$data['contact']->alias;?>">
                    
                    <input class="glow disabled w100" type="text" name="username" title="Username" tabindex="2" placeholder="Username" value="<?=$data['contact']->username;?>" disabled>
                    
                    <input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="4" value="Save">
                    <input type="hidden" name="token" value="<?=$data['token'];?>">
                    
                    <br>
                    <div class="message-error"><?=$data['error'];?></div>
                    
                    <a href="<?=$data['BASE_HREF'];?>/conversations">Return</a>
                </form>
            </div>
