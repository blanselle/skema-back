vcl 4.0;

import std;

backend default {
  .host = "php";
  .port = "80";
}

# Hosts allowed to send BAN requests
acl invalidators {
  "localhost";
  "php";
  "varnish";

  # local Kubernetes network
  "10.0.0.0"/8;
  "172.16.0.0"/12;
  "192.168.0.0"/16;
}

sub vcl_recv {
  if (req.restarts > 0) {
    set req.hash_always_miss = true;
  }

  # Remove the "Forwarded" HTTP header if exists (security)
  unset req.http.forwarded;

  # To allow API Platform to ban by cache tags
  if (req.method == "BAN") {
    if (client.ip !~ invalidators) {
      return (synth(405, "Not allowed"));
    }

    if (req.http.ApiPlatform-Ban-Regex) {
      ban("obj.http.Cache-Tags ~ " + req.http.ApiPlatform-Ban-Regex);
      ban("obj.http.url ~ " + req.http.ApiPlatform-Ban-Regex);

      return (synth(200, "Ban added"));
    }

    return (synth(400, "ApiPlatform-Ban-Regex HTTP header must be set."));
  }

  # For health checks
  if (req.method == "GET" && req.url == "/healthz") {
    return (synth(200, "OK"));
  }

  # Do not cache authentication
  if (req.url ~ "^/$" || req.url ~ "/api/authentication_token$") {
    return (pass) ;
  }

  # Do not cache document_reference folder
  if (req.url ~ "^/documents/document_reference/") {
    return (pass) ;
  }

  if (req.url ~ "\.(jpe?g|png|gif|pdf|gz|tgz|bz2|tbz|tar|zip|tiff|tif|svg|swf|ico|mp3|mp4|m4a|ogg|mov|avi|wmv|flv|xls|vsd|doc|ppt|pps|vsd|doc|ppt|pps|xls|pdf|sxw|rar|odc|odb|odf|odg|odi|odp|ods|odt|sxc|sxd|sxi|sxw|dmg|torrent|deb|msi|iso|rpm|css|js)$") {
    return (hash);
  }

  if (req.method == "GET" && (req.url ~ "^/api/media/[0-9]+/render$")) {
    unset req.http.Cookie;
  }

  if (req.method == "GET" && (req.url ~ "^/api/oral_test_students/[0-9]+/check$")) {
    unset req.http.Cookie;
    unset req.http.Authorization;
  }

  if (req.method == "GET" && (req.url ~ "^/api/students/[0-9]+/landing$")) {
    unset req.http.Cookie;
    unset req.http.Authorization;
  }
}

sub vcl_hit {
  if (obj.ttl >= 0s) {
    # A pure unadulterated hit, deliver it
    return (deliver);
  }

  if (std.healthy(req.backend_hint)) {
    # The backend is healthy
    # Fetch the object from the backend
    return (restart);
  }

  # No fresh object and the backend is not healthy
  if (obj.ttl + obj.grace > 0s) {
    # Deliver graced object
    # Automatically triggers a background fetch
    return (deliver);
  }

  # No valid object to deliver
  # No healthy backend to handle request
  # Return error
  return (synth(503, "API is down"));
}

sub vcl_deliver {
  # Don't send cache tags related headers to the client
  unset resp.http.url;
  # Comment the following line to send the "Cache-Tags" header to the client (e.g. to use CloudFlare cache tags)
  unset resp.http.Cache-Tags;

  if (obj.hits > 0) {
    set resp.http.X-Cache = obj.hits + " HITS";
  } else {
    set resp.http.X-Cache = "MISS";
  }

  unset resp.http.Via;
  unset resp.http.Server;
  unset resp.http.X-Varnish;
  unset resp.http.X-Powered-By;

  set resp.http.X-Powered-By = "Skema";
  set resp.http.X-Server = server.hostname;
  set resp.http.X-Backend-Status = resp.status;
  set resp.http.X-Info= req.http.X-Variable;
  set resp.http.X-Forwarded-For = req.http.X-Forwarded-For;
  set resp.http.X-Platform = req.http.X-Platform;
  set resp.http.X-UA-Device = req.http.X-UA-Device;
  set resp.http.Strict-Transport-Security = "max-age=31536000; includeSubDomains";
  if (resp.http.Age) {
    set resp.http.X-Cache-TTL-Age = resp.http.Age;
  }
}

sub vcl_backend_response {
  # Ban lurker friendly header
  set beresp.http.url = bereq.url;

  # Update default ttl
  set beresp.ttl = 2h;

  # Add a grace in case the backend is down
  set beresp.grace = 1h;

  # Happens after we have read the response headers from the backend.
  #
  # Here you clean the response headers, removing silly Set-Cookie headers
  # and other mistakes your backend does.
  if (beresp.ttl <= 0s
    || beresp.http.Set-Cookie
    || beresp.http.Surrogate-control ~ "no-store"
    || (!beresp.http.Surrogate-Control && beresp.http.Cache-Control ~ "no-cache|no-store|private")
    || beresp.http.Vary == "*"
  ) {
    set beresp.ttl = 24h;
  }

  if ((beresp.status >= 500 && beresp.status < 600) || beresp.status == 404 || beresp.status == 401) {
    unset beresp.http.Cache-Control;
    set beresp.http.Cache-Control = "no-cache, max-age=0, must-revalidate";
    set beresp.ttl = 0s;
    set beresp.http.Pragma = "no-cache";
    set beresp.uncacheable = true;
  }

  if (bereq.url ~ "\.(jpe?g|png|gif|pdf|gz|tgz|bz2|tbz|tar|zip|tiff|tif)$" ||
      bereq.url ~ "\.(svg|swf|ico|mp3|mp4|m4a|ogg|mov|avi|wmv|flv)$" ||
      bereq.url ~ "\.(xls|vsd|doc|ppt|pps|vsd|doc|ppt|pps|xls|pdf|sxw|rar|odc|odb|odf|odg|odi|odp|ods|odt|sxc|sxd|sxi|sxw|dmg|torrent|deb|msi|iso|rpm)$") {
    set beresp.ttl = std.duration(beresp.http.age+"s",0s) + 100d;
    set beresp.http.cache-control = "public, max-age=8640000";
    unset beresp.http.cookie;
    set beresp.storage_hint = "static";
    set beresp.http.x-storage = "static";
  } elseif (bereq.url ~ "service-worker.js") {
    set beresp.ttl = std.duration(beresp.http.age+"s",0s);
    set beresp.do_gzip = true;
    set beresp.http.X-Cache = "ZIP";
    unset beresp.http.Cache-Control;
    unset beresp.http.Expires;
    unset beresp.http.cookie;
    set beresp.storage_hint = "static";
    set beresp.http.x-storage = "static";
  } elseif (bereq.url ~ "\.(css|js)$") {
    set beresp.ttl = std.duration(beresp.http.age+"s",0s) + 100d;
    set beresp.do_gzip = true;
    set beresp.http.X-Cache = "ZIP";
    unset beresp.http.cookie;
    set beresp.storage_hint = "static";
    set beresp.http.x-storage = "static";
  } else {
    set beresp.do_gzip = true;
    set beresp.http.X-Cache = "ZIP";
    unset beresp.http.Cache-Control;
    unset beresp.http.Expires;
    set beresp.storage_hint = "default";
    set beresp.http.x-storage = "default";
  }

  if (beresp.status == 206 && beresp.http.Content-Range != "") {
    set beresp.http.CR = beresp.http.Content-Range;
  }

  return (deliver);
}