<?php

declare(strict_types=1);

namespace Extended\API;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Validator;
use WP_REST_Request;
use WP_User;

final class Request extends WP_REST_Request
{
    /** Get the input from the request as a boolean. */
    public function boolean($key = null, bool $default = false): bool
    {
        return filter_var(
            $this->input($key, $default),
            FILTER_VALIDATE_BOOLEAN,
        );
    }

    /** Get data from the request as collection. */
    public function collect(string $key, $default = null): Collection
    {
        return new Collection($this->input($key, $default));
    }

    /** Get the input from the request. */
    public function input(string $key, $default = null)
    {
        return data_get($this->get_params(), $key, $default);
    }

    /** Get the input from the request as an integer. */
    public function integer($key, $default = 0)
    {
        return intval($this->input($key, $default));
    }

    /** Determine if the request contains a given input key. */
    public function has(string $key): bool
    {
        return $this->has_param($key);
    }

    /** Get the current user making the request. */
    public function user(): WP_User|null
    {
        $user = wp_get_current_user();

        return $user?->ID !== 0 ? $user : null;
    }

    /** Validate the request attributes. */
    public function validate(array $rules): array
    {
        $loader = new FileLoader(new Filesystem(), [__DIR__ . '/../lang']);
        $translator = new Translator($loader, 'en');
        $validator = new Validator($translator, $this->get_params(), $rules);

        return $validator->validate();
    }

    /** Create a new request from a WP_REST_Request instance. */
    public static function fromWordPressRestRequest(
        WP_REST_Request $restRequest,
    ): self {
        $request = new self(
            $restRequest->get_method(),
            $restRequest->get_route(),
            $restRequest->get_attributes(),
        );

        foreach ($restRequest->get_params() as $key => $value) {
            $request->set_param($key, $value);
        }

        $request->set_headers($restRequest->get_headers());

        return $request;
    }
}
