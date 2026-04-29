<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $cvVersion->name }} - Preview</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #1a1a1a;
            max-width: 210mm;
            margin: 0 auto;
            padding: 20mm;
        }
        h1 { font-size: 24px; margin-bottom: 4px; }
        h2 { font-size: 18px; margin-bottom: 8px; color: #333; border-bottom: 1px solid #e5e5e5; padding-bottom: 4px; }
        h3 { font-size: 16px; margin-bottom: 4px; }
        p { margin-bottom: 8px; }
        .subtitle { color: #666; font-size: 14px; margin-bottom: 16px; }
        .section { margin-bottom: 20px; }
        .entry { margin-bottom: 12px; }
        .entry-header { display: flex; justify-content: space-between; align-items: baseline; }
        .entry-title { font-weight: 600; }
        .entry-org { color: #555; }
        .entry-date { color: #888; font-size: 12px; white-space: nowrap; }
        .entry-location { color: #666; font-size: 13px; }
        .entry-description { margin-top: 4px; white-space: pre-wrap; }
        .skills-list { display: flex; flex-wrap: wrap; gap: 6px; }
        .skill-tag { background: #f0f0f0; padding: 2px 8px; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>
    @php
        $sections = $cvVersion->resolvedSections ?? collect();
    @endphp

    {{-- Header / Name --}}
    <header class="section">
        <h1>{{ $cvVersion->name }}</h1>
        @if($cvVersion->target_role)
            <p class="subtitle">{{ $cvVersion->target_role }}</p>
        @endif
    </header>

    @foreach($sections as $section)
        <div class="section">
            <h2>{{ $section->title ?? ucfirst($section->type->value) }}</h2>

            @if($section->type->value === 'summary')
                <p>{!! nl2br(e($section->description ?? '')) !!}</p>

            @elseif($section->type->value === 'skill')
                <div class="skills-list">
                    @php
                        $skills = is_array($section->meta) ? ($section->meta['skills'] ?? [$section->title]) : [$section->title];
                    @endphp
                    @foreach($skills as $skill)
                        <span class="skill-tag">{{ $skill }}</span>
                    @endforeach
                </div>

            @else
                <div class="entry">
                    <div class="entry-header">
                        <div>
                            <span class="entry-title">{{ $section->title }}</span>
                            @if($section->organization)
                                <span class="entry-org"> · {{ $section->organization }}</span>
                            @endif
                        </div>
                        @if($section->start_date)
                            <span class="entry-date">
                                {{ $section->start_date->format('M Y') }} – {{ $section->is_current ? 'Present' : ($section->end_date?->format('M Y') ?? '') }}
                            </span>
                        @endif
                    </div>
                    @if($section->location)
                        <div class="entry-location">{{ $section->location }}</div>
                    @endif
                    @if($section->description)
                        <div class="entry-description">{!! nl2br(e($section->description)) !!}</div>
                    @endif
                </div>
            @endif
        </div>
    @endforeach
</body>
</html>
