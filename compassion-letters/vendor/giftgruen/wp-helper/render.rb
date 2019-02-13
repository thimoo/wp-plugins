#!/usr/bin/env ruby

require 'github/markdown'

puts "<style>"
puts File.read("lib/gh.css")
puts "</style>"

puts GitHub::Markdown.render_gfm File.read(ARGV[0])
