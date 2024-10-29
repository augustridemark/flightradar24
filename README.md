# Instructions
The only dependency is having php installed and being able to run it as CLI to start a server

1. Add a TOKEN to the app.env file that will be used for authentication of the API
2. Navigate to the project root and start a basic php server
```
cd flightradar24/
php -S localhost:8000
```
3. Use curl or other tool to interact with the API. The following header is required with the token defined in app.env
```
-H 'Authorization: Basic <TOKEN>'
```

## Notes
- Only one ticket can be created at once and it is stored in a simple file storage on disk
- Use `ticket_id` from create ticket endpoint to interact with cancel and change seat endpoints

## Example requests

### Create a ticket
```
curl -X POST \
  'http://localhost:8000/api/createTicket' \
  -H 'Authorization: Basic <TOKEN>' \
  -H 'Content-Type: application/json' \
  -d '{
    "passport_id":"AA9090999",
    "departure_airport":"ARN",
    "destination_airport":"AKL"
}'
```
Response success
```
{
  "ticket_id": "1f98990da6c8da961aec5fb13d89c0c6",
  "passport_id": "AA9090999",
  "departure_airport": "ARN",
  "destination_airport": "AKL",
  "departure_time": "2024-10-28 21:12",
  "seat": "B2"
}
```
Response error
```
{
  "error": "Missing required ticket data for: departure_airport"
}
```

### Cancel a ticket
```
curl  -X POST \
  'http://localhost:8000/api/cancelTicket' \
  -H 'Authorization: Basic <TOKEN>' \
  -H 'Content-Type: application/json' \
  -d '{
    "ticket_id":"f1eb1d93461493060f1e7bdab19e01a1"
}'
```
Response success
```
{
  "ticket_id": "d69325461c2f51ce80cf588e900f0072",
  "canceled": true
}
```
Response error
```
{
  "ticket_id": "d69325461c2f51ce80cf588e900f0072",
  "canceled": false,
  "error": "Ticket not found"
}
```


### Change seats
```
curl  -X POST \
  'http://localhost:8000/api/changeSeat' \
  -H 'Authorization: Basic <TOKEN>' \
  -H 'Content-Type: application/json' \
  -d '{
    "ticket_id":"1f98990da6c8da961aec5fb13d89c0c6"
}'
```
Response success
```
{
  "ticket_id": "59d1c65668a0011b2c8bb044625fedb6",
  "passport_id": "AA9090999",
  "departure_airport": "ARN",
  "destination_airport": "AKL",
  "departure_time": "2024-10-28 21:29",
  "seat": "D20"
}
```
Response error
```
{
  "ticket_id": "59d1c65668a0011b2c8bb044625feb6",
  "error": "Ticket not found"
}
```