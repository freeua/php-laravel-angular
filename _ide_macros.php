<?php

namespace Illuminate\Contracts\Routing {

    class ResponseFactory
    {
        /**
         * Return a new success API response from the application.
         *
         * @param array|object $payload
         * @param string       $message
         * @param int          $code
         * @param array        $headers
         * @param int          $options
         *
         * @return \Illuminate\Http\JsonResponse
         * @instantiated
         */
        public function success(
            $payload = [],
            $message = '',
            $code = 200,
            $headers = [
            ],
            $options = 0
        ) {

        }

        /**
         * Return a new failed API response from the application.
         *
         * @param array|object $payload
         * @param string       $message
         * @param int          $code
         * @param array        $headers
         * @param int          $options
         *
         * @return \Illuminate\Http\JsonResponse
         * @instantiated
         */
        public function error(
            $payload,
            $message = '',
            $code = 400,
            $headers = [
            ],
            $options = 0
        ) {

        }
    }
}

namespace Illuminate\Http {

    class Request
    {
        /**
         * Return application key from request header.
         *
         * @return string
         * @instantiated
         */
        public function appKey()
        {

        }

        /**
         * Return company slug from request header.
         *
         * @return string
         * @instantiated
         */
        public function companySlug()
        {

        }
    }
}
