{ pkgs ? import (fetchTarball "https://github.com/NixOS/nixpkgs/tarball/nixos-24.05") {} }:

pkgs.mkShellNoCC {
	packages = with pkgs; [
		darkhttpd
        php
	];
	shellHook = ''
		export DEV_ENVIRONMENT="list-editor"
		export NIX_SHELL_PATH="$(pwd)"
		source ~/.bashrc

		alias start-server="php -c $NIX_SHELL_PATH -S 127.0.0.1:8080"
	'';
}
