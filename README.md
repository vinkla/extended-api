```php
namespace App\Http;

use Extended\API\Request;
use Extended\API\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestException;

class ContactController 
{
  public function __invoke(Request $request): Response
  {
    // Validate data and return the validated fields.
    $request->validate([
      'name' => 'required|string',
      'email' => 'required|email',
    ]);

    $request->input('name'); // Get the input value.
    $request->integer('name'); // Get the input value as integer.
    $request->float('name'); // Get the input value as float.
    $request->boolean('name'); // Get the input value as boolean.
    $request->all(); // Get all input values.
    $request->only(['name']); // Get only specific input values.
    $request->except(['name']); // Get all input values except specific ones.
    $request->has('name'); // Check if input exists.
    $request->filled('name'); // Check if input is filled.
    $request->missing('name'); // Check if input is missing.
    $request->collect(); // Get input as collection.
    $request->query(); // Get input from the query string.
    $request->user(); // Get the current user.
    $request->dd(); // Dump and die.
    $request->dump(); // Dump.

    // Return a response.
    abort(404);

    // All HTTP exception are caught and returned as JSON.
    throw new BadRequestException('Invalid request');

    return response($request->only('name', 'email'));
  }
}

register_extended_rest_request('acme', 'contacts', [
  'callback' => App\Http\ContactController::class,
  'permission_callback' => '__return_true',
])
```

```php
add_filter('wp_php_error_message', function ($message, $error): string {
    if (WP_DEBUG) {
        return explode("\n", $error['message'])[0];
    }

    return $message;
}, 10, 2);
```

