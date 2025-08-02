<<<<<<< HEAD
<<<<<<< HEAD
<?php

namespace Stripe;

/**
 * Interface for a Stripe client.
 */
interface StripeStreamingClientInterface extends BaseStripeClientInterface
{
    public function requestStream($method, $path, $readBodyChunkCallable, $params, $opts);
}
=======
=======
>>>>>>> e52701a (Update existing files from local folder)
<?php

namespace Stripe;

/**
 * Interface for a Stripe client.
 */
interface StripeStreamingClientInterface extends BaseStripeClientInterface
{
    public function requestStream($method, $path, $readBodyChunkCallable, $params, $opts);
}
<<<<<<< HEAD
>>>>>>> origin/main
=======
>>>>>>> e52701a (Update existing files from local folder)
