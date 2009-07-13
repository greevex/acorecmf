<?php
Core::GetModule('users')->Quit();
header('Location: ' . Core::$data['lang_root']);
exit();