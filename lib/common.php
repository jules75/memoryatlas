<?php

function generate_token() {
    return sha1(mt_rand().mt_rand().mt_rand());
}