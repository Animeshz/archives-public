# Axios RFCs

## What is an RFC?
[what-is-an-rfc]: #what-is-an-rfc

The "RFC" (request for comments) is a process to taking inputs from public in any matter that will substantially change or add in to the way the things currently work.

Formally, a simple & crisp design document in markdown is submitted as a PR by an author, and everybody is RFC'd (requested for comments) in that PR and the author is supposed to update the document as discussion moves forwards...

Throughout, a finalization happens, in that case RFC is either merged or rejected.

## Why do you need to do this
[why-do-you-need-to-do-this]: #why-do-you-need-to-do-this

You are always _choosing_, every moment of the day. You'll take a lot of decisions as a part of technical society and witness even more being made. Decisions ranging from minor matters like determining the format of communication or organizing events, to more impactful ones that could significantly shape the way thing currently work.

Recalling the reason behind a decision can be painfully hard after months of ongoing efforts, often because you're missing the context that led up to it. Furthermore, the lack of documentation also makes onboarding next batch and handover processes more difficult as you might find yourself struggling to explain decisions made in the past. Especially when wings are growing and a lot of progress has already been made previously, this will become strikingly obvious.

## Raising and writing an RFC
[raising-and-writing-an-rfc]: #raising-and-writing-an-rfc

In short, to get a major reform happen in the Axios, one must first get the RFC merged into the RFC repo as a markdown file. At that point the RFC is 'active' and may be implemented with the goal of eventual inclusion into Axios.

### The Process
[the-process]: #the-process

1.  Work on your proposal in a Markdown file based on the template (`0000-template.md`) found in this repo.

    - Fork this repo.
    - Create your proposal as `rfcs/0000-my-feature.md` (where "my-feature" is descriptive).
    - Submit a pull request.

2.  Build consensus.

    - RFC will be discussed, everybody is free to put their opinion, as much as possible in the comment thread of the pull request itself.
    - Author will actively look for comments, reply and after fruitful discussion update the rfc as required.

3.  Finalization

    - A team of 3-4 members, who actively participated or are most familiar with the topic guide the discussion and help out author in making informed decisions.
    - A senior member or higher will merge the PR, at which point the RFC will become 'active'.

## Taking part in an RFC
[taking-part-in-an-rfc]: #taking-part-in-an-rfc

Quite simple, go to the 'Files changed' section in the PR, and based on your views add comments to conflicting lines from your viewpoint.

## License

This repository is currently in the process of being licensed under [Creative Commons Attribution Share Alike 4.0 International](https://spdx.org/licenses/CC-BY-SA-4.0.html) (SPDX Identifier CC-BY-SA-4.0). That is, you're free to share & adapt all the content, as long as you give attrubution, not impose any additional restriction from anything the license permits, and keep the same license.

