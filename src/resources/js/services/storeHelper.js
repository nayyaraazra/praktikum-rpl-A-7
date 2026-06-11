/**
 * Helper utility to determine if a store is currently open based on its operating hours string.
 * Operating hours string format example: "Senin - Sabtu, 08:00 - 20:00" or "Setiap Hari, 09:00 - 17:00"
 * Evaluated under Asia/Jakarta timezone.
 */
export function isStoreOpen(operatingHours) {
  if (!operatingHours) {
    return true; // default to open if not set
  }

  try {
    const now = new Date();
    
    // Get current day name and time in Asia/Jakarta timezone
    const dayFormatter = new Intl.DateTimeFormat('id-ID', {
      timeZone: 'Asia/Jakarta',
      weekday: 'long'
    });
    const rawDayName = dayFormatter.format(now).toLowerCase(); // e.g. "senin" or "jum'at"
    
    const timeFormatter = new Intl.DateTimeFormat('en-US', {
      timeZone: 'Asia/Jakarta',
      hour: '2-digit',
      minute: '2-digit',
      hourCycle: 'h23'
    });
    const timeStr = timeFormatter.format(now); // e.g. "13:28"
    const [currH, currM] = timeStr.split(':').map(Number);
    const currentMinutes = currH * 60 + currM;

    // Map Indonesian day names/abbreviations to standardized codes
    const weekdayMapping = {
      'senin': 'sen', 'sen': 'sen',
      'selasa': 'sel', 'sel': 'sel',
      'rabu': 'rab', 'rab': 'rab',
      'kamis': 'kam', 'kam': 'kam',
      'jumat': 'jum', "jum'at": 'jum', 'jum': 'jum',
      'sabtu': 'sab', 'sab': 'sab',
      'minggu': 'min', 'min': 'min'
    };

    // Clean rawDayName from any quotes or non-alphabetic chars for mapping key lookup
    const cleanDayName = rawDayName.replace(/[^a-z]/gi, '');
    const currentDayCode = weekdayMapping[cleanDayName] || weekdayMapping[rawDayName] || rawDayName.substring(0, 3);

    let str = operatingHours.replace(/\s+/g, ' ').trim();

    // Find all times in HH:MM or HH.MM format
    const timeMatches = str.match(/(\d{2}[:\.]\d{2})/g);
    if (!timeMatches || timeMatches.length < 2) {
      return true; // if times cannot be parsed, default to open
    }

    const openTimeStr = timeMatches[0].replace('.', ':');
    const closeTimeStr = timeMatches[1].replace('.', ':');

    const parts = str.split(',');
    const daysPart = parts.length > 1 ? parts[0] : str;
    const lowerDays = daysPart.toLowerCase();

    let openEveryDay = false;
    let openDays = [];

    if (lowerDays.includes('setiap hari') || lowerDays.includes('setiap-hari')) {
      openEveryDay = true;
    } else {
      const weekdayOrder = ['sen', 'sel', 'rab', 'kam', 'jum', 'sab', 'min'];
      // Match range pattern like "senin - sabtu" or "senin-sabtu" or using en-dash
      const rangePattern = /(senin|selasa|rabu|kamis|jumat|sabtu|minggu|sen|sel|rab|kam|jum|sab|min)\s*[\-–]\s*(senin|selasa|rabu|kamis|jumat|sabtu|minggu|sen|sel|rab|kam|jum|sab|min)/;
      const rangeMatch = lowerDays.match(rangePattern);

      if (rangeMatch) {
        const startKey = weekdayMapping[rangeMatch[1]];
        const endKey = weekdayMapping[rangeMatch[2]];
        if (startKey && endKey) {
          const startIndex = weekdayOrder.indexOf(startKey);
          const endIndex = weekdayOrder.indexOf(endKey);
          if (startIndex !== -1 && endIndex !== -1) {
            let i = startIndex;
            while (true) {
              openDays.push(weekdayOrder[i]);
              if (i === endIndex) {
                break;
              }
              i = (i + 1) % 7;
            }
          }
        }
      } else {
        // Parse individual days separated by comma, space, slash, etc.
        const words = lowerDays.split(/[\s,\/]+/);
        for (const w of words) {
          const cleanWord = w.replace(/[^a-z']/gi, '');
          if (weekdayMapping[cleanWord]) {
            const dayVal = weekdayMapping[cleanWord];
            if (!openDays.includes(dayVal)) {
              openDays.push(dayVal);
            }
          }
        }
      }
    }

    if (!openEveryDay) {
      if (!openDays.includes(currentDayCode)) {
        return false;
      }
    }

    const [openH, openM] = openTimeStr.split(':').map(Number);
    const openMinutes = openH * 60 + openM;

    const [closeH, closeM] = closeTimeStr.split(':').map(Number);
    const closeMinutes = closeH * 60 + closeM;

    if (openMinutes <= closeMinutes) {
      return currentMinutes >= openMinutes && currentMinutes <= closeMinutes;
    } else {
      // Handles overnight hours (e.g. 22:00 - 04:00)
      return currentMinutes >= openMinutes || currentMinutes <= closeMinutes;
    }

  } catch (e) {
    console.error('Error parsing operating hours:', e);
    return true; // fallback to open
  }
}
