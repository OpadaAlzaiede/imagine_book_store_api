<?php

namespace App\Http\Services\UserResolver;


class SessionUserResolver implements UserResolver {

    public function get() {

        return auth()->user();
    }
}
