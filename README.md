# Skema project

Installation
------------

```
cd run/dev
make build
```

OpenApi est accessible sur http://localhost:8081/api/docs


Cache
-----

Varnish est installé en local.
Pour passer par le système de cache, l'url de l'api devient http://localhost

Redis
-----

Pour accéder à redis
cd /run/dev
make redis

Ligne de commande redis
se rendre sur le redis-cli
>redis-cli

lancer les statistiques
redis-cli --stat

Vérifier le monitoring
redis-cli monitoring

Payment
-----

Simuler le webhook Ogone

Prenez soin de changer les valeurs suivantes: __TYPE__, __MERCHANT_REFERENCE__, __EXTERNAL_PAYMENT_ID__, __STATUS__

```console
curl --location --request POST 'http://127.0.0.1:8081/api/payments/event?token=3rgD8fGXPshn1OAm5TB9quapcEGGMFlAcI4keyhg9jD66HR7bO7Q9ZL_AHKd6uHpnxFBjS8XxR5lOpbleizkBw' \
--header 'Content-Type: application/json' \
--data-raw '[
  {
    "apiVersion": "v1",
    "created": "2020-12-09T11:20:40.3744722+01:00",
    "id": "34b8a607-1fce-4003-b3ae-a4d29e92b232",
    "merchantId": "SKEMAAST",
    "payment": {
      "paymentOutput": {
        "amountOfMoney": {
          "amount": 6000,
          "currencyCode": "EUR"
        },
        "references": {
          "merchantReference": "MERCHANT_REFERENCE"
        },
        "cardPaymentMethodSpecificOutput": {
          "paymentProductId": 1,
          "card": {
            "cardNumber": "************1111",
            "expiryDate": "0122"
          },
          "fraudResults": {
            "fraudServiceResult": "no-advice"
          },
          "threeDSecureResults": {
            "eci": "9"
          }
        },
        "paymentMethod": "card"
      },
      "status": "STATUS",
      "statusOutput": {
        "isCancellable": false,
        "statusCategory": "CREATED",
        "statusCode": 0,
        "isAuthorized": false,
        "isRefundable": false
      },
      "id": "EXTERNAL_PAYMENT_ID"
    },
    "type": "payment.created"
  }
]'
```