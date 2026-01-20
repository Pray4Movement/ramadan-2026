#!/bin/bash

# Bible Verse Fetcher - Uses API.Bible
# Usage:
#   ./get-verse.sh "Genesis 1:1-5"              # Fetch verse (default Bible)
#   ./get-verse.sh "John 3:16" BIBLE_ID         # Fetch with specific Bible
#   ./get-verse.sh --list-bibles en             # List English Bibles
#   ./get-verse.sh --list-bibles ar             # List Arabic Bibles

set -e

# Your API key (get free at https://scripture.api.bible)
BIBLE_API_KEY="cCltIr7bTQ3SWhrU0QM-8"

API_BASE="https://rest.api.bible/v1"
DEFAULT_BIBLE="de4e12af7f28f599-02"  # ESV

# Book name to API.Bible abbreviation
get_book_abbr() {
    local book="$1"
    case "$book" in
        genesis|gen) echo "GEN" ;;
        exodus|exod|ex) echo "EXO" ;;
        leviticus|lev) echo "LEV" ;;
        numbers|num) echo "NUM" ;;
        deuteronomy|deut) echo "DEU" ;;
        joshua|josh) echo "JOS" ;;
        judges|judg) echo "JDG" ;;
        ruth) echo "RUT" ;;
        "1 samuel"|1samuel|"1 sam"|1sam) echo "1SA" ;;
        "2 samuel"|2samuel|"2 sam"|2sam) echo "2SA" ;;
        "1 kings"|1kings|"1 kgs"|1kgs) echo "1KI" ;;
        "2 kings"|2kings|"2 kgs"|2kgs) echo "2KI" ;;
        "1 chronicles"|1chronicles|"1 chr"|1chr) echo "1CH" ;;
        "2 chronicles"|2chronicles|"2 chr"|2chr) echo "2CH" ;;
        ezra) echo "EZR" ;;
        nehemiah|neh) echo "NEH" ;;
        esther|esth) echo "EST" ;;
        job) echo "JOB" ;;
        psalms|psalm|ps|psa) echo "PSA" ;;
        proverbs|prov|pro) echo "PRO" ;;
        ecclesiastes|eccl|eccles) echo "ECC" ;;
        "song of solomon"|song|songs|sos) echo "SNG" ;;
        isaiah|isa) echo "ISA" ;;
        jeremiah|jer) echo "JER" ;;
        lamentations|lam) echo "LAM" ;;
        ezekiel|ezek) echo "EZK" ;;
        daniel|dan) echo "DAN" ;;
        hosea|hos) echo "HOS" ;;
        joel) echo "JOL" ;;
        amos) echo "AMO" ;;
        obadiah|obad) echo "OBA" ;;
        jonah) echo "JON" ;;
        micah|mic) echo "MIC" ;;
        nahum|nah) echo "NAM" ;;
        habakkuk|hab) echo "HAB" ;;
        zephaniah|zeph) echo "ZEP" ;;
        haggai|hag) echo "HAG" ;;
        zechariah|zech) echo "ZEC" ;;
        malachi|mal) echo "MAL" ;;
        matthew|matt|mt) echo "MAT" ;;
        mark|mk) echo "MRK" ;;
        luke|lk) echo "LUK" ;;
        john|jn) echo "JHN" ;;
        acts) echo "ACT" ;;
        romans|rom) echo "ROM" ;;
        "1 corinthians"|1corinthians|"1 cor"|1cor) echo "1CO" ;;
        "2 corinthians"|2corinthians|"2 cor"|2cor) echo "2CO" ;;
        galatians|gal) echo "GAL" ;;
        ephesians|eph) echo "EPH" ;;
        philippians|phil) echo "PHP" ;;
        colossians|col) echo "COL" ;;
        "1 thessalonians"|1thessalonians|"1 thess"|1thess) echo "1TH" ;;
        "2 thessalonians"|2thessalonians|"2 thess"|2thess) echo "2TH" ;;
        "1 timothy"|1timothy|"1 tim"|1tim) echo "1TI" ;;
        "2 timothy"|2timothy|"2 tim"|2tim) echo "2TI" ;;
        titus) echo "TIT" ;;
        philemon|phlm) echo "PHM" ;;
        hebrews|heb) echo "HEB" ;;
        james|jas) echo "JAS" ;;
        "1 peter"|1peter|"1 pet"|1pet) echo "1PE" ;;
        "2 peter"|2peter|"2 pet"|2pet) echo "2PE" ;;
        "1 john"|1john) echo "1JN" ;;
        "2 john"|2john) echo "2JN" ;;
        "3 john"|3john) echo "3JN" ;;
        jude) echo "JUD" ;;
        revelation|rev) echo "REV" ;;
        *) echo "" ;;
    esac
}

check_api_key() {
    if [ -z "$BIBLE_API_KEY" ] || [ "$BIBLE_API_KEY" = "YOUR_API_KEY_HERE" ]; then
        echo "Error: Set your API key at the top of this script" >&2
        echo "Get a free key at https://scripture.api.bible" >&2
        exit 1
    fi
}

list_bibles() {
    local lang="$1"
    check_api_key

    echo "Fetching Bibles for language: $lang" >&2

    curl -s -H "api-key: $BIBLE_API_KEY" \
        "$API_BASE/bibles?language=$lang" | \
        jq -r '.data[] | "\(.id)\t\(.name)\t\(.language.name)"' 2>/dev/null || \
        echo "Error fetching Bibles. Check your API key." >&2
}

parse_reference() {
    local ref="$1"

    # Normalize: lowercase, replace en-dash/em-dash with hyphen
    ref=$(echo "$ref" | tr '[:upper:]' '[:lower:]' | sed 's/[–—]/-/g')

    # Extract book name (everything before chapter:verse)
    local book
    local cv

    # Handle books starting with numbers (1 john, 2 peter, etc)
    if echo "$ref" | grep -qE '^[0-9]'; then
        book=$(echo "$ref" | sed -E 's/^([0-9] ?[a-z]+) .*/\1/')
    else
        book=$(echo "$ref" | sed -E 's/^([a-z ]+[a-z]) [0-9].*/\1/' | sed 's/ *$//')
    fi

    # Get remaining part (chapter:verse)
    cv=$(echo "$ref" | sed "s/^$book //" | sed 's/^ *//')

    # Lookup book abbreviation
    local book_abbr
    book_abbr=$(get_book_abbr "$book")

    if [ -z "$book_abbr" ]; then
        echo "Unknown book: $book" >&2
        return 1
    fi

    # Parse chapter and verses
    local chapter
    local verses
    chapter=$(echo "$cv" | sed -E 's/^([0-9]+):.*/\1/')
    verses=$(echo "$cv" | sed -E 's/^[0-9]+://')

    # Format: BOOK.chapter.startverse-BOOK.chapter.endverse
    if echo "$verses" | grep -q '-'; then
        local start_verse
        local end_verse
        start_verse=$(echo "$verses" | cut -d'-' -f1)
        end_verse=$(echo "$verses" | cut -d'-' -f2)
        echo "$book_abbr.$chapter.$start_verse-$book_abbr.$chapter.$end_verse"
    else
        echo "$book_abbr.$chapter.$verses"
    fi
}

fetch_verse() {
    local ref="$1"
    local bible_id="${2:-$DEFAULT_BIBLE}"

    check_api_key

    local passage_id
    passage_id=$(parse_reference "$ref") || exit 1

    local url="$API_BASE/bibles/$bible_id/passages/$passage_id?content-type=text&include-notes=false&include-titles=false&include-chapter-numbers=false&include-verse-numbers=false"

    local response
    response=$(curl -s -H "api-key: $BIBLE_API_KEY" "$url")

    # Check for errors
    if echo "$response" | jq -e '.error' >/dev/null 2>&1; then
        echo "API Error: $(echo "$response" | jq -r '.message // .error')" >&2
        exit 1
    fi

    # Extract and clean content
    echo "$response" | jq -r '.data.content' | \
        sed 's/^ *//;s/ *$//' | \
        tr '\n' ' ' | \
        sed 's/  */ /g' | \
        sed 's/^ *//;s/ *$//'

    echo ""
}

# Main
case "${1:-}" in
    --list-bibles)
        if [ -z "${2:-}" ]; then
            echo "Usage: $0 --list-bibles LANGUAGE_CODE" >&2
            echo "Examples: en, ar, fr, es, id, zh" >&2
            exit 1
        fi
        list_bibles "$2"
        ;;
    --help|-h|"")
        echo "Bible Verse Fetcher - Uses API.Bible"
        echo ""
        echo "Usage:"
        echo "  $0 \"Genesis 1:1-5\"              # Fetch verse (ESV default)"
        echo "  $0 \"John 3:16\" BIBLE_ID         # Fetch with specific Bible"
        echo "  $0 --list-bibles en             # List English Bibles"
        echo "  $0 --list-bibles ar             # List Arabic Bibles"
        echo ""
        echo "Default Bible: ESV ($DEFAULT_BIBLE)"
        ;;
    *)
        fetch_verse "$1" "${2:-}"
        ;;
esac
