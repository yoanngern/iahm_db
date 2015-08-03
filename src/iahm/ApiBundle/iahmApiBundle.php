<?php

namespace iahm\ApiBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class iahmApiBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSOAuthServerBundle';
    }
}
