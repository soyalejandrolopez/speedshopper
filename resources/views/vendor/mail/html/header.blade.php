@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<span style="font-size: 26px;">🛍️</span>
<span style="color: #312e81; font-size: 20px; font-weight: 700;">{!! $slot !!}</span>
</a>
</td>
</tr>
