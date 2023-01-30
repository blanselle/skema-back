FROM varnish:stable
COPY ./default.vcl /etc/varnish/
CMD ["varnishd", "-F", "-f", "/etc/varnish/default.vcl", "-p", "http_resp_hdr_len=1024k", "-p", "http_resp_size=1024k", "-p", "http_req_hdr_len=64k", "-p", "workspace_backend=256k", "-p", "workspace_client=256k", "-p", "http_max_hdr=256"]
