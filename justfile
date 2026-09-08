# inanepain/event
# version: $Id$
# date: $Date$

set shell := ["zsh", "-cu"]
set positional-arguments

project := 'inane\\event'

# list recipes
_default:
    @echo "{{project}}:"
    @just --list --list-heading ''

# start
_start task='':
    @echo "{{project}}: {{GREEN}}start{{NORMAL}}: {{task}}"

# done
_done task='':
    @echo "{{project}}: {{GREEN}}done{{NORMAL}} {{task}}"

# git push all
[group: 'GIT']
git-push-all: (_start "Push All") && (_done "Push All")
    #!/usr/bin/env zsh
    git pushall

#region DOC
# compile asciidoc files
[group: 'doc']
build:
	#!/usr/bin/env zsh
	echo "project: {{MAGENTA}}{{project}}{{NORMAL}} => Building documentation..."
	just build-changelog
	just build-readme
	echo "{{MAGENTA}}{{project}}{{NORMAL}}: documentation {{BOLD + RED + UNDERLINE}}built{{NORMAL}}"

# Build changelog
[group: 'doc']
build-changelog: && (compile "changelog")

# Build readme
[group: 'doc']
build-readme: && (compile "readme")

# compile final asciidoc file: changelog, readme
[group: 'doc']
[arg('target', pattern='changelog|readme', help="Build final document from source docs.")]
compile target="changelog": (_start target) && (_done target)
	#!/usr/bin/env zsh
	echo "\tBuilding {{CYAN}}{{uppercase(target)}}{{NORMAL}}.adoc..."
	rm -f {{uppercase(target)}}.adoc
	asciidoctor-reducer.bat -o {{uppercase(target)}}.adoc doc/{{target}}/index.adoc
	asciidoctor.bat -b docbook {{uppercase(target)}}.adoc
	rm -f {{uppercase(target)}}.xml
	echo "\t{{uppercase(target)}}.adoc {{RED}}done.{{NORMAL}}"
#endregion DOC
