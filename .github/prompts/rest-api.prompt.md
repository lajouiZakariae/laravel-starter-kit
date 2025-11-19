---
agent: agent
name: rest-api
description: Laravel CRUD API Generation Prompt
model: GPT-4.1 (copilot)
argument-hint: "Enter the name of the new resource (e.g., Booking) and the path to a reference controller (e.g., App/Http/Controllers/Api/V1/UserController.php)."
---

Your goal is to generate a **complete Laravel CRUD API** for the **`${input:resourceName:Booking}`** resource, following the conventions of the existing codebase. This implementation must use a **hybrid approach**: Laravel Form Requests for validation and Spatie Data Objects for typed input handling and automatic mapping via attributes.

### 📝 Resource Details and Rules

**New Resource:** **${input:resourceName:Booking}**
**Reference Controller:** The structure, naming, and code style must match the controller found at: **${input:referencePath:App/Http/Controllers/Api/V1/UserController.php}**

**Schema Rules for the ${input:resourceName:Booking} Model (Used in Request Validation):**

-   `user_id`: Foreign Key referencing the `users` table. **Required**.
-   `room_id`: Foreign Key referencing the `rooms` table. **Required**.
-   `check_in_date`: `date` type. **Required**.
-   `check_out_date`: `date` type. Must be after `check_in_date`. **Required**.
-   `total_price`: `decimal(8, 2)` type. **Required**.
-   `status`: `string(20)`. **Required**. Must be one of: `'pending'`, `'confirmed'`, `'cancelled'`.

---

### 🎯 Hybrid Input Requirements

1.  **Form Requests (Validation/Authorization):**

    -   Generate **`Store${input:resourceName:Booking}Request`** and **`Update${input:resourceName:Booking}Request`**.
    -   These Requests must contain **all validation and authorization logic** based on the schema rules.

2.  **Data Object (Mapping/Typing):**

    -   Generate a single Data Object: **`${input:resourceName:Booking}Data`**.
    -   Define all properties within this object using **camelCase** (e.g., `checkInDate`, `totalPrice`).
    -   **Crucially:** Apply the **`#[MapName(SnakeCaseMapper::class)]`** attribute directly above the class definition to ensure automatic mapping from camelCase properties to snake_case database columns. _The Data Object must NOT include a `rules()` method._

3.  **Controller Integration:**
    -   In the controller's `store` and `update` methods:
        -   Inject the corresponding Form Request (`$request`).
        -   **Map the validated data** to the Data Object using its static factory method: **`$data = ${input:resourceName:Booking}Data::from($request->validated());`**
        -   The model persistence should use `$data->toArray()` for saving.

---

### ⚙️ Required Files and Tasks

1.  Generate the **Migration** file (`create_bookings_table`).
2.  Generate the **`${input:resourceName:Booking}` Model** with appropriate `$fillable` properties and relationships.
3.  Generate the **`Store${input:resourceName:Booking}Request`** and **`Update${input:resourceName:Booking}Request`** classes.
4.  Generate the **`${input:resourceName:Booking}Data`** class, including **camelCase** properties and the **`#[MapName(SnakeCaseMapper::class)]`** attribute.
5.  Generate the **`App\Http\Controllers\Api\V1\BookingController`** using the hybrid input approach.
6.  Generate the **`${input:resourceName:Booking}Resource`** class.
7.  **Register the API route** for the new resource in `routes/api.php` under the `/v1/` group.

**Output Format:** Provide the complete, final code for **each file** created or modified in separate, clearly labeled code blocks.
